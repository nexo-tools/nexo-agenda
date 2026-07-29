<?php

use Illuminate\Support\Facades\Blade;

// Guardian: the shared chrome is present and wired. Covers: the ecosystem
// registry exists, the app-switcher renders it (with the hub invite), and the
// footer renders the canonical attribution (set + unset).

it('exposes the ecosystem registry', function () {
    expect(config('nexo-ecosystem.tools'))->toBeArray()->not->toBeEmpty();
    expect(config('nexo-ecosystem.hub_url'))->toBeString()->not->toBeEmpty();
});

it('renders the app-switcher with every tool and the hub invite', function () {
    $html = view('components.nexo-app-switcher')->render();

    expect($html)->toContain('nexo-menu');
    foreach (config('nexo-ecosystem.tools') as $tool) {
        expect($html)->toContain($tool['name']);
    }
    // The standing bridge to the hub is always present.
    expect($html)->toContain(config('nexo-ecosystem.hub_url'))
        ->and($html)->toContain(config('nexo-ecosystem.github_org_url'));
});

it('renders the powered-by attribution from config', function () {
    config()->set('nexo.attribution.label', 'example.test');
    config()->set('nexo.attribution.url', 'https://example.test');

    // Rendered as a component so its ComponentAttributeBag ($attributes) is bound.
    $html = Blade::render('<x-nexo-footer />');

    expect($html)->toContain('example.test')
        ->and($html)->toContain('https://example.test');
});

it('falls back to the product label, never the upstream author, when unset', function () {
    // A third-party instance that sets no attribution must not end up
    // advertising alvarocdev.com (add-branding-footer: open-source
    // multi-instance products carry a neutral product default).
    // Both explicitly, so the assertion does not depend on whoever runs it
    // having (or not having) NEXO_ATTRIBUTION_* in their local .env.
    config()->set('nexo.attribution.label', null);
    config()->set('nexo.attribution.url', null);

    $html = Blade::render('<x-nexo-footer />');

    expect($html)->toContain('made with Nexo Agenda');
    expect($html)->not->toContain('href="https://alvarocdev.com"');
});
