<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusiness
{
    /**
     * The owner area requires a business. An authenticated owner without one —
     * e.g. a fresh Nexo ID SSO sign-up, whose OIDC claims carry no category/city —
     * is redirected to onboarding instead of 500ing on the many
     * $user->business->... dereferences across the /app controllers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->business === null) {
            return redirect()->route('onboarding.create');
        }

        return $next($request);
    }
}
