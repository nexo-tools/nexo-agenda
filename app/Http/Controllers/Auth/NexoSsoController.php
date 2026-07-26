<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\NexoSso\NexoSsoLinkRefusedException;
use App\Services\NexoSso\NexoSsoService;
use App\Services\NexoSso\NexoSsoUserResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

class NexoSsoController extends Controller
{
    /** Start the flow: PKCE pair + state in session, redirect to the provider. */
    public function redirect(NexoSsoService $sso): RedirectResponse
    {
        abort_unless(config('nexo-sso.enabled'), 404); // AC-CFG-1 (defense in depth)

        $state = Str::random(40);
        $verifier = Str::random(64); // 43–128 chars per RFC 7636
        $nonce = Str::random(40); // echoed back as the id_token nonce claim (AC-NONCE-1)
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

        session([
            'nexo_sso.state' => $state,
            'nexo_sso.verifier' => $verifier,
            'nexo_sso.nonce' => $nonce,
        ]);

        try {
            $url = $sso->buildAuthorizeUrl($state, $challenge, $nonce); // discovery may hit the network
        } catch (Throwable) {
            // Provider unreachable: friendly failure, never a 500. (AC-DEGRADE-2)
            return redirect()
                ->route('login')
                ->withErrors(['nexo_sso' => __('El inicio de sesión con Nexo ID no está disponible por ahora. Inténtalo de nuevo más tarde.')]);
        }

        return redirect()->away($url);
    }

    /** Provider redirected back: verify state, exchange code, validate id_token, log in locally. */
    public function callback(Request $request, NexoSsoService $sso, NexoSsoUserResolver $resolver): RedirectResponse
    {
        abort_unless(config('nexo-sso.enabled'), 404); // AC-CFG-1 (defense in depth)

        $state = (string) $request->session()->pull('nexo_sso.state', '');
        $verifier = (string) $request->session()->pull('nexo_sso.verifier', '');
        $nonce = (string) $request->session()->pull('nexo_sso.nonce', '');
        $returnedState = (string) $request->query('state', '');

        // CSRF check happens before any provider call. (AC-FLOW-2)
        if ($state === '' || $returnedState === '' || ! hash_equals($state, $returnedState) || ! $request->filled('code')) {
            return redirect()
                ->route('login')
                ->withErrors(['nexo_sso' => __('No se pudo validar la solicitud de inicio de sesión. Inténtalo de nuevo.')]);
        }

        try {
            $tokens = $sso->exchangeCode((string) $request->query('code'), $verifier);
            $claims = $sso->validateIdToken((string) ($tokens['id_token'] ?? ''), $nonce); // AC-FLOW-3, AC-NONCE-1
            $user = $resolver->resolve($claims);
        } catch (NexoSsoLinkRefusedException) {
            return redirect()
                ->route('login')
                ->withErrors(['nexo_sso' => __('Ya existe una cuenta con este correo. Verifica tu correo en Nexo ID primero.')]); // AC-LINK-2
        } catch (Throwable $e) {
            // Invalid token or provider down mid-flow: safe error, no session. (AC-FLOW-3, AC-DEGRADE-2)
            report($e);

            return redirect()
                ->route('login')
                ->withErrors(['nexo_sso' => __('Falló el inicio de sesión con Nexo ID. Inténtalo de nuevo.')]);
        }

        // Tool-owned session, independent of the provider's lifetime. (AC-SESS-1)
        Auth::login($user);
        $request->session()->regenerate();

        // Keep the raw id_token for RP-initiated logout (id_token_hint). It lets
        // the provider identify the session AND validate post_logout_redirect_uri
        // against this client's registered URIs. (AC-LOGOUT-1)
        if (! empty($tokens['id_token'])) {
            $request->session()->put('nexo_sso.id_token', (string) $tokens['id_token']);
        }

        // Land on the owner dashboard; a business-less SSO owner is then sent to
        // onboarding by the EnsureBusiness middleware. Adaptation point (template README).
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Local logout with RP-initiated (front-channel) central logout. Ends the
     * tool session first, then — when SSO is enabled and the provider advertises
     * an end_session_endpoint — hands the browser off to it so the Nexo ID session
     * ends too. Best-effort: a provider hiccup never blocks the local logout that
     * already happened, and a provider that advertises no end_session simply lands
     * on the local post-logout page. (AC-LOGOUT-1)
     */
    public function logout(Request $request, NexoSsoService $sso): RedirectResponse
    {
        $idTokenHint = (string) $request->session()->pull('nexo_sso.id_token', '');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (config('nexo-sso.enabled')) {
            try {
                $endSessionUrl = $sso->buildEndSessionUrl($idTokenHint !== '' ? $idTokenHint : null);
                if ($endSessionUrl !== null) {
                    return redirect()->away($endSessionUrl);
                }
            } catch (Throwable) {
                // Provider unreachable while building the URL: fall back to the
                // local post-logout page — the tool session is already gone.
            }
        }

        return redirect()->route('login');
    }
}
