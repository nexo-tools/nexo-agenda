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

it('renders the attribution label verbatim, with nothing prepended', function () {
    // The label IS the whole phrase. A tool that prepends its own
    // __('nexo.footer.powered_by') ships "Made by powered by example.test".
    // The old assertion used a bare domain, which reads fine either way and
    // hid exactly that bug in production for months — so assert the rendered
    // anchor text, not just that the label appears somewhere.
    config()->set('nexo.attribution.label', 'powered by example.test');
    config()->set('nexo.attribution.url', 'https://example.test');

    // Rendered as a component so its ComponentAttributeBag ($attributes) is bound.
    $html = Blade::render('<x-nexo-footer />');

    expect($html)->toContain('https://example.test');
    expect(preg_match('/<a[^>]*href="https:\/\/example\.test"[^>]*>\s*powered by example\.test\s*<\/a>/', $html))
        ->toBe(1, 'The attribution anchor must contain exactly the label.');

    // Belt and braces: no tool may reintroduce a prefix key.
    foreach (['Hecho por', 'Made by', 'Feito por'] as $prefix) {
        expect(str_contains($html, $prefix))
            ->toBeFalse("The footer prepends \"{$prefix}\" to the attribution label.");
    }
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
