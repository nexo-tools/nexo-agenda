<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Silent SSO trigger (OIDC prompt=none). On a guest HTML navigation, hand the
 * browser once per session to the silent authorize attempt: a visitor with a
 * live Nexo ID session comes back logged in without clicking anything; anyone
 * else comes straight back untouched (the provider answers `login_required`
 * without rendering any UI). (AC-SILENT-1..5)
 *
 * Loop guard: the attempt is marked in the session BEFORE redirecting, and the
 * redirect-back lands in that same session, so a second attempt never fires —
 * neither on the bounce-back nor on any later pageload of the session.
 *
 * Bot/first-hit guard: only requests that already present this app's session
 * cookie are eligible. Crawlers and uptime monitors don't send cookies, so
 * public pages keep serving them plain 200s; a first-ever visit sets the
 * cookie and the attempt happens on the next navigation. (AC-SILENT-4)
 *
 * Install (per tool): append to the `web` middleware group. Standalone mode is
 * untouched — with SSO (or the silent switch) off this is a pass-through.
 */
final class NexoSsoSilentLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldAttempt($request)) {
            $request->session()->put('nexo_sso.silent_attempted', true);
            redirect()->setIntendedUrl($request->fullUrl());

            return redirect()->route('nexo-sso.silent');
        }

        return $next($request);
    }

    private function shouldAttempt(Request $request): bool
    {
        return config('nexo-sso.enabled')
            && config('nexo-sso.silent')
            && $request->isMethod('GET')
            && ! $request->expectsJson()
            && $request->hasSession()
            && ! $request->session()->get('nexo_sso.silent_attempted', false)
            && auth()->guest()
            && $request->cookies->has((string) config('session.cookie'))
            && ! $this->isExcluded($request);
    }

    /** Surfaces where a bounce is never acceptable: paths and named routes from config. */
    private function isExcluded(Request $request): bool
    {
        if ($request->is('auth/nexo/*') || $request->is(config('nexo-sso.silent_excluded', []))) {
            return true;
        }

        $routes = config('nexo-sso.silent_excluded_routes', []);

        return $routes !== [] && $request->routeIs($routes);
    }
}
