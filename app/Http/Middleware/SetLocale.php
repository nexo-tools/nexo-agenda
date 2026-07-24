<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** @var list<string> */
    private const SUPPORTED = ['es', 'en', 'pt'];

    /**
     * Locale priority: explicit ?lang= choice, then the shared `nexo-lang` cookie
     * (scoped to the parent domain so the choice crosses every ecosystem tool),
     * then the visitor's browser language, then the app default. The resolved
     * locale is persisted back to `nexo-lang` (domain .alvarocdev.com in prod;
     * host-only in local/dev). The cookie is excluded from encryption in
     * bootstrap/app.php so every tool can read it.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang');

        if (! is_string($locale) || ! in_array($locale, self::SUPPORTED, true)) {
            $locale = $request->cookie('nexo-lang');
        }
        if (! is_string($locale) || ! in_array($locale, self::SUPPORTED, true)) {
            $locale = $request->getPreferredLanguage(self::SUPPORTED);
        }
        if (! is_string($locale) || ! in_array($locale, self::SUPPORTED, true)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);

        if ($request->query('lang') || ! $request->cookie('nexo-lang')) {
            $host = $request->getHost();
            $domain = str_ends_with($host, 'alvarocdev.com') ? '.alvarocdev.com' : null;
            Cookie::queue(cookie('nexo-lang', $locale, 525600, '/', $domain, $request->secure(), false, false, 'lax'));
        }

        return $next($request);
    }
}
