<?php

use App\Models\Business;

it('sends security headers on public pages', function () {
    $business = Business::factory()->create();

    $response = $this->get('/'.$business->slug);

    $response->assertOk();
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
    expect($response->headers->get('Permissions-Policy'))->toContain('camera=()');
});

it('sends a self-contained content-security-policy', function () {
    $response = $this->get('/');

    $csp = $response->headers->get('Content-Security-Policy');

    expect($csp)
        ->toContain("default-src 'self'")
        ->toContain("object-src 'none'")
        ->toContain("frame-ancestors 'none'")
        ->toContain("base-uri 'self'")
        ->toContain("form-action 'self'");

    // The only permitted external host is the Nexo Tools hub (opt-in cookieless
    // beacon in connect-src); no other external host leaks into the policy.
    expect($csp)->toContain("connect-src 'self' https://nexotools.alvarocdev.com");
    expect(str_replace('https://nexotools.alvarocdev.com', '', $csp))
        ->not->toContain('http://')
        ->not->toContain('https://');
});

it('does not advertise HSTS over plain http', function () {
    $response = $this->get('/');

    expect($response->headers->has('Strict-Transport-Security'))->toBeFalse();
});

it('keeps the .htaccess CSP in sync with the middleware CSP', function () {
    // On LiteSpeed the web server strips the PHP-sent CSP (Force-HTTPS), so it is
    // re-asserted in public/.htaccess. The two must match or prod silently weakens.
    $middlewareCsp = $this->get('/')->headers->get('Content-Security-Policy');

    $htaccess = file_get_contents(public_path('.htaccess'));
    preg_match('/Header always set Content-Security-Policy "([^"]*)"/', $htaccess, $m);

    expect($m[1] ?? null)->not->toBeNull()
        ->and($m[1])->toBe($middlewareCsp);
});

it('ships no inline event handlers, which our own CSP would refuse to run', function () {
    // script-src carries no 'unsafe-inline' and no 'unsafe-hashes', so an
    // onchange/onsubmit attribute is dead code in production: the date pickers
    // and directory filters silently stopped submitting, and the confirm() in
    // front of every destructive action never ran — the delete just happened.
    // Alpine (allowed via 'unsafe-eval') is where that behaviour belongs.
    $offenders = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('views'), FilesystemIterator::SKIP_DOTS)) as $file) {
        if (! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }

        // Mail templates are rendered by mail clients, not by this CSP.
        if (str_contains(str_replace('\\', '/', $file->getPathname()), '/views/emails/')) {
            continue;
        }

        if (preg_match_all('/\son(?:change|submit|click|input|load|focus|blur|keydown|keyup|mouseover)\s*=/i', (string) file_get_contents($file->getPathname()), $m)) {
            $offenders[] = $file->getPathname().' -> '.implode(', ', array_unique($m[0]));
        }
    }

    expect($offenders)->toBe([], "Inline event handlers are blocked by our CSP:\n".implode("\n", $offenders));
});
