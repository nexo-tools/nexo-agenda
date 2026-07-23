<?php

// Shared support for the NexoSso suite: a throwaway RSA keypair, a fake OIDC
// provider (discovery/JWKS/token) and an id_token signer. require_once'd by
// each test file — functions are guarded for repeat inclusion.

use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;

if (! function_exists('nexoSsoKeypair')) {
    /** @return array{private: string, public: string, jwk: array<string, string>} */
    function nexoSsoKeypair(): array
    {
        static $pair = null;
        if ($pair !== null) {
            return $pair;
        }

        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($key, $privatePem);
        $details = openssl_pkey_get_details($key);

        $b64url = fn (string $bin): string => rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');

        return $pair = [
            'private' => $privatePem,
            // The PEM of the PUBLIC key — used by the RS/HS confusion test, which
            // signs an HS256 token with this public material as the HMAC secret.
            'public' => $details['key'],
            'jwk' => [
                'kty' => 'RSA',
                'alg' => 'RS256',
                'use' => 'sig',
                'kid' => 'nexo-test-key',
                'n' => $b64url($details['rsa']['n']),
                'e' => $b64url($details['rsa']['e']),
            ],
        ];
    }
}

if (! function_exists('nexoSsoClaims')) {
    /**
     * The default id_token claim set; override any claim (null removes it).
     *
     * @return array<string, mixed>
     */
    function nexoSsoClaims(array $overrides = []): array
    {
        $claims = array_merge([
            'iss' => config('nexo-sso.issuer'),
            'aud' => config('nexo-sso.client_id'),
            'sub' => 'user-uuid-0001',
            'exp' => time() + 300,
            'iat' => time(),
            'email' => 'user@example.com',
            'email_verified' => true,
            'name' => 'Test User',
        ], $overrides);

        return array_filter($claims, fn ($value) => $value !== null);
    }
}

if (! function_exists('nexoSsoIdToken')) {
    /**
     * Signed id_token with sane defaults; override any claim (null removes it).
     * Pass $kid=null to emit a token with NO `kid` header — the real Nexo ID
     * production path (its bridge signs kid-less), which the kid-less single-key
     * branch of NexoSsoService::validateIdToken must handle.
     */
    function nexoSsoIdToken(array $overrides = [], ?string $signWithPem = null, ?string $kid = 'nexo-test-key'): string
    {
        return JWT::encode(nexoSsoClaims($overrides), $signWithPem ?? nexoSsoKeypair()['private'], 'RS256', $kid);
    }
}

if (! function_exists('nexoSsoUnsignedToken')) {
    /**
     * A forged `alg=none` id_token (header alg=none, empty signature) — the
     * classic algorithm-stripping attack. Must be rejected: no session may be
     * created from an unsigned token.
     */
    function nexoSsoUnsignedToken(array $overrides = []): string
    {
        $b64url = fn (string $raw): string => rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
        $header = $b64url((string) json_encode(['typ' => 'JWT', 'alg' => 'none']));
        $payload = $b64url((string) json_encode(nexoSsoClaims($overrides)));

        return $header.'.'.$payload.'.';
    }
}

if (! function_exists('nexoSsoFakeProvider')) {
    /** Fakes discovery + JWKS + token endpoints. Pass the token response body (or a Closure/callable fake). */
    function nexoSsoFakeProvider(?array $tokenResponse = null): void
    {
        $issuer = config('nexo-sso.issuer');
        $tokenResponse ??= ['access_token' => 'fake-access-token', 'token_type' => 'Bearer', 'id_token' => nexoSsoIdToken()];

        Http::fake([
            $issuer.'/.well-known/openid-configuration' => Http::response([
                'issuer' => $issuer,
                'authorization_endpoint' => $issuer.'/oauth/authorize',
                'token_endpoint' => $issuer.'/oauth/token',
                'userinfo_endpoint' => $issuer.'/oauth/userinfo',
                'jwks_uri' => $issuer.'/oauth/jwks',
            ]),
            $issuer.'/oauth/jwks' => Http::response(['keys' => [nexoSsoKeypair()['jwk']]]),
            $issuer.'/oauth/token' => Http::response($tokenResponse),
        ]);
    }
}

if (! function_exists('nexoSsoCallback')) {
    /** Drives the callback with a consistent state/verifier session, as if the provider redirected back. */
    function nexoSsoCallback(TestCase $test, string $code = 'auth-code'): TestResponse
    {
        return $test
            ->withSession(['nexo_sso.state' => str_repeat('s', 40), 'nexo_sso.verifier' => str_repeat('v', 64)])
            ->get(route('nexo-sso.callback', ['code' => $code, 'state' => str_repeat('s', 40)]));
    }
}
