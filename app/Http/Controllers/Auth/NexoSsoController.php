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

        try {
            $url = $this->buildAuthorizeRedirect($sso);
        } catch (Throwable) {
            // Provider unreachable: friendly failure, never a 500. (AC-DEGRADE-2)
            return redirect()
                ->route('login')
                ->withErrors(['nexo_sso' => __('Sign-in with Nexo ID is temporarily unavailable. Please try again later.')]);
        }

        return redirect()->away($url);
    }

    /**
     * Silent attempt (OIDC prompt=none): same authorize request plus
     * `prompt=none`, marked in the session so it runs at most once per session
     * (the NexoSsoSilentLogin middleware checks the mark BEFORE redirecting
     * here — no retry loop). Every failure here is invisible by design: the
     * user never asked for anything, so on any hiccup they just continue to
     * where they were going. (AC-SILENT-1, AC-SILENT-2)
     */
    public function silent(Request $request, NexoSsoService $sso): RedirectResponse
    {
        abort_unless(config('nexo-sso.enabled') && config('nexo-sso.silent'), 404);

        // Idempotent (the middleware already set it); also covers direct hits.
        $request->session()->put('nexo_sso.silent_attempted', true);

        try {
            $url = $this->buildAuthorizeRedirect($sso, prompt: 'none');
        } catch (Throwable) {
            // Provider unreachable: continue to the destination, no error UI.
            return redirect()->intended('/');
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
        $silent = (bool) $request->session()->pull('nexo_sso.silent', false);
        $returnedState = (string) $request->query('state', '');

        // OIDC error response — the provider declined without issuing a code.
        // After a silent attempt, `login_required`/`interaction_required` is the
        // NORMAL "no Nexo ID session" answer: stay guest, keep the once-per-
        // session mark (already set), continue to the destination with no error
        // UI. No login happens on this path, so no state/code checks apply.
        // (AC-SILENT-2)
        if ($request->filled('error')) {
            if ($silent) {
                return redirect()->intended('/');
            }

            return redirect()
                ->route('login')
                ->withErrors(['nexo_sso' => __('Sign-in with Nexo ID failed. Please try again.')]);
        }

        // CSRF check happens before any provider call. (AC-FLOW-2)
        if ($state === '' || $returnedState === '' || ! hash_equals($state, $returnedState) || ! $request->filled('code')) {
            if ($silent) {
                return redirect()->intended('/'); // silent flows never surface errors
            }

            return redirect()
                ->route('login')
                ->withErrors(['nexo_sso' => __('The sign-in request could not be validated. Please try again.')]);
        }

        try {
            $tokens = $sso->exchangeCode((string) $request->query('code'), $verifier);
            $claims = $sso->validateIdToken((string) ($tokens['id_token'] ?? ''), $nonce); // AC-FLOW-3, AC-NONCE-1
            $user = $resolver->resolve($claims);
        } catch (NexoSsoLinkRefusedException) {
            if ($silent) {
                return redirect()->intended('/'); // they'll see it if they sign in interactively
            }

            return redirect()
                ->route('login')
                ->withErrors(['nexo_sso' => __('An account with this email already exists. Verify your email on Nexo ID first.')]); // AC-LINK-2
        } catch (Throwable $e) {
            // Invalid token or provider down mid-flow: safe error, no session. (AC-FLOW-3, AC-DEGRADE-2)
            report($e);

            if ($silent) {
                return redirect()->intended('/');
            }

            return redirect()
                ->route('login')
                ->withErrors(['nexo_sso' => __('Sign-in with Nexo ID failed. Please try again.')]);
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

        // A logout must stick: mark the FRESH session so the silent attempt
        // doesn't immediately sign the user back in on the next guest page.
        // (AC-SILENT-5)
        $request->session()->put('nexo_sso.silent_attempted', true);

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

    /**
     * Shared authorize-request builder: mints state/verifier/nonce, stores them
     * in the session, returns the provider URL. (AC-FLOW-1, AC-NONCE-1)
     */
    private function buildAuthorizeRedirect(NexoSsoService $sso, ?string $prompt = null): string
    {
        $state = Str::random(40);
        $verifier = Str::random(64); // 43–128 chars per RFC 7636
        $nonce = Str::random(40); // echoed back as the id_token nonce claim (AC-NONCE-1)
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

        session([
            'nexo_sso.state' => $state,
            'nexo_sso.verifier' => $verifier,
            'nexo_sso.nonce' => $nonce,
            // Marks THIS authorize request as silent or interactive, so the
            // callback picks the right failure UX. Always (re)set: a stale
            // silent mark from an abandoned attempt must not turn a later
            // interactive flow's errors invisible.
            'nexo_sso.silent' => $prompt === 'none',
        ]);

        return $sso->buildAuthorizeUrl($state, $challenge, $nonce, $prompt); // discovery may hit the network
    }
}
