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
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

        session([
            'nexo_sso.state' => $state,
            'nexo_sso.verifier' => $verifier,
        ]);

        try {
            $url = $sso->buildAuthorizeUrl($state, $challenge); // discovery may hit the network
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
        $returnedState = (string) $request->query('state', '');

        // CSRF check happens before any provider call. (AC-FLOW-2)
        if ($state === '' || $returnedState === '' || ! hash_equals($state, $returnedState) || ! $request->filled('code')) {
            return redirect()
                ->route('login')
                ->withErrors(['nexo_sso' => __('No se pudo validar la solicitud de inicio de sesión. Inténtalo de nuevo.')]);
        }

        try {
            $tokens = $sso->exchangeCode((string) $request->query('code'), $verifier);
            $claims = $sso->validateIdToken((string) ($tokens['id_token'] ?? '')); // AC-FLOW-3
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

        // Land on the owner dashboard; a business-less SSO owner is then sent to
        // onboarding by the EnsureBusiness middleware. Adaptation point (template README).
        return redirect()->intended(route('dashboard'));
    }
}
