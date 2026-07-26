<?php

declare(strict_types=1);

namespace App\Services\NexoSso;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

final class NexoSsoService
{
    /**
     * Discovery document of the configured issuer, cached. Endpoints are never
     * hard-coded: a self-hosted instance may lay them out differently. (AC-CFG-2)
     *
     * @return array<string, mixed>
     */
    public function discovery(): array
    {
        return Cache::remember(
            'nexo-sso.discovery',
            config('nexo-sso.discovery_ttl'),
            fn (): array => $this->http()
                ->get(config('nexo-sso.issuer').'/.well-known/openid-configuration')
                ->throw()
                ->json()
        );
    }

    /**
     * Authorization request: code + PKCE(S256) + state + nonce. (AC-FLOW-1, AC-NONCE-1)
     *
     * $prompt: OIDC `prompt` value — `none` performs a silent attempt (the
     * provider returns the code with no UI when a session exists, or bounces
     * straight back with `error=login_required` when it doesn't). (AC-SILENT-1)
     */
    public function buildAuthorizeUrl(string $state, string $codeChallenge, string $nonce, ?string $prompt = null): string
    {
        return $this->discovery()['authorization_endpoint'].'?'.http_build_query(array_filter([
            'client_id' => config('nexo-sso.client_id'),
            'redirect_uri' => route('nexo-sso.callback'),
            'response_type' => 'code',
            'scope' => config('nexo-sso.scopes'),
            'state' => $state,
            // Replay/injection guard: the provider echoes this back as the id_token
            // `nonce` claim; the callback rejects a token whose nonce doesn't match
            // the one this browser sent. (AC-NONCE-1)
            'nonce' => $nonce,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'prompt' => $prompt,
        ], fn ($value): bool => $value !== null));
    }

    /**
     * RP-initiated (front-channel) logout URL, per OIDC session management. Null
     * when the provider advertises no `end_session_endpoint` (discovery) — callers
     * then fall back to a local-only logout. The URL carries:
     *   - id_token_hint: the raw id_token from login, so the provider can identify
     *     the session AND the client whose registered URIs bound the redirect.
     *   - post_logout_redirect_uri: where the provider sends the browser back.
     *
     * Contract (enforced provider-side, see Nexo ID end_session controller): the
     * provider MUST validate post_logout_redirect_uri against the URIs registered
     * for the id_token_hint's client and MUST NOT open-redirect. An unregistered
     * (or hint-less) URI lands on the provider's own "signed out" page instead.
     */
    public function buildEndSessionUrl(?string $idTokenHint): ?string
    {
        $endpoint = $this->discovery()['end_session_endpoint'] ?? null;
        if (! is_string($endpoint) || $endpoint === '') {
            return null;
        }

        $params = array_filter([
            'id_token_hint' => $idTokenHint,
            'post_logout_redirect_uri' => config('nexo-sso.post_logout_redirect_uri'),
        ], fn ($value): bool => is_string($value) && $value !== '');

        return $params === [] ? $endpoint : $endpoint.'?'.http_build_query($params);
    }

    /**
     * Exchange the authorization code (server-side, with the PKCE verifier).
     *
     * @return array<string, mixed>
     */
    public function exchangeCode(string $code, string $codeVerifier): array
    {
        return $this->http()
            ->asForm()
            ->post($this->discovery()['token_endpoint'], [
                'grant_type' => 'authorization_code',
                'client_id' => config('nexo-sso.client_id'),
                'redirect_uri' => route('nexo-sso.callback'),
                'code' => $code,
                'code_verifier' => $codeVerifier,
            ])
            ->throw()
            ->json();
    }

    /**
     * Validate the id_token: RS256 signature against the JWKS, exp (enforced by
     * JWT::decode), iss, aud and nonce. Any failure throws — no session gets
     * created from an unverified token. (AC-FLOW-3, AC-NONCE-1)
     *
     * $expectedNonce is the nonce this browser sent to /authorize (kept in the
     * session). A missing/empty expected nonce, a token with no nonce claim, or a
     * mismatch all reject: an id_token minted for a different authorize request
     * cannot be replayed into this callback.
     *
     * @return array<string, mixed> the verified claims
     */
    public function validateIdToken(string $idToken, string $expectedNonce): array
    {
        if ($idToken === '') {
            throw new NexoSsoException('Missing id_token in token response.');
        }

        try {
            $jwks = $this->jwks();
            $header = json_decode(JWT::urlsafeB64Decode(explode('.', $idToken)[0]), true) ?: [];

            // Nexo ID's bridge signs without a kid header; with a single-key
            // JWKS that's unambiguous, so use the key directly. With a kid (or
            // multiple keys) defer to the keyset lookup.
            $decoded = empty($header['kid']) && count($jwks['keys'] ?? []) === 1
                ? JWT::decode($idToken, JWK::parseKey($jwks['keys'][0], 'RS256'))
                : JWT::decode($idToken, JWK::parseKeySet($jwks, 'RS256'));
        } catch (Throwable $e) {
            throw new NexoSsoException('id_token verification failed: '.$e->getMessage(), previous: $e);
        }

        /** @var array<string, mixed> $claims */
        $claims = json_decode((string) json_encode($decoded), true);

        if (($claims['iss'] ?? null) !== config('nexo-sso.issuer')) {
            throw new NexoSsoException('id_token issuer mismatch.');
        }

        $audience = (array) ($claims['aud'] ?? []);
        if (! in_array(config('nexo-sso.client_id'), $audience, true)) {
            throw new NexoSsoException('id_token audience mismatch.');
        }

        // Bind the token to this browser's authorize request. (AC-NONCE-1)
        $nonce = (string) ($claims['nonce'] ?? '');
        if ($expectedNonce === '' || $nonce === '' || ! hash_equals($expectedNonce, $nonce)) {
            throw new NexoSsoException('id_token nonce mismatch.');
        }

        return $claims;
    }

    /**
     * @return array<string, mixed>
     */
    public function userinfo(string $accessToken): array
    {
        return $this->http()
            ->withToken($accessToken)
            ->get($this->discovery()['userinfo_endpoint'])
            ->throw()
            ->json();
    }

    /** @return array<string, mixed> */
    private function jwks(): array
    {
        return Cache::remember(
            'nexo-sso.jwks',
            config('nexo-sso.jwks_ttl'),
            fn (): array => $this->http()
                ->get($this->discovery()['jwks_uri'])
                ->throw()
                ->json()
        );
    }

    private function http(): PendingRequest
    {
        $timeout = config('nexo-sso.timeout');

        return Http::timeout($timeout)->connectTimeout($timeout);
    }
}
