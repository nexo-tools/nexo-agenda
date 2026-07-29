<?php

// Guardian: the brand assets derived from the tool's mark exist and are wired
// into the chrome. Without this, deleting public/og-image.png (or forgetting to
// run generate-brand-assets.mjs after a mark change) keeps the suite green and
// only shows up as a broken favicon / blank OG card in production.
//
// Copy into tests/Feature/Nexo/. Adjust $required if a tool legitimately ships a
// different set (e.g. no PWA icons); the ecosystem marks come from
// config/nexo-ecosystem.php — the app-switcher renders one <img> per tool.

it('ships the brand assets derived from the tool mark', function () {
    $required = [
        'favicon.ico',
        'favicon.svg',
        'apple-touch-icon.png',
        'icon-192.png',
        'icon-512.png',
        'og-image.png',
        'site.webmanifest',
    ];

    $missing = array_values(array_filter(
        $required,
        fn (string $asset) => ! is_file(public_path($asset))
    ));

    expect($missing)->toBe([], 'Missing brand assets in public/ (run scripts/generate-brand-assets.mjs): '.implode(', ', $missing));
});

it('ships an isotype for every tool in the ecosystem registry', function () {
    /** @var array<string, array{mark?: string}> $tools */
    $tools = config('nexo-ecosystem.tools', []);

    expect($tools)->not->toBeEmpty('The ecosystem registry is empty — the app-switcher would render nothing.');

    $missing = [];
    foreach ($tools as $key => $tool) {
        $mark = $tool['mark'] ?? null;
        if ($mark === null || ! is_file(public_path(ltrim($mark, '/')))) {
            $missing[] = $key.' -> '.($mark ?? 'no mark configured');
        }
    }

    expect($missing)->toBe([], "Ecosystem marks missing from public/ (copy from nexo-brand/marks/):\n".implode("\n", $missing));
});

it('wires the favicon and the OG image into the page chrome', function () {
    // public/ is served by the web server, never by the router, so an HTTP call
    // to /favicon.svg is a 404 inside the test kernel no matter how healthy the
    // file is. What the suite can prove is the other half of the failure mode:
    // the assets exist on disk (test above) AND the head actually points at them.
    $html = $this->get('/')->assertOk()->getContent();

    expect(str_contains($html, '/favicon.svg'))->toBeTrue('The <head> does not link /favicon.svg.');
    expect(str_contains($html, url('/og-image.png')))->toBeTrue('og:image does not point at /og-image.png.');
});
