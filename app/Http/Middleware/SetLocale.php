<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['es', 'en', 'pt'];

    public function handle(Request $request, Closure $next): Response
    {
        $requested = $request->query('lang');

        if (is_string($requested) && in_array($requested, self::SUPPORTED, true)) {
            $request->session()->put('locale', $requested);
        }

        $locale = $request->session()->get('locale')
            ?? $request->getPreferredLanguage(self::SUPPORTED)
            ?? config('app.locale');

        app()->setLocale($locale);

        return $next($request);
    }
}
