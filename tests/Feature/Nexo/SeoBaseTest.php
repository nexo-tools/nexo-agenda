<?php

// Guardian: the public "outside" of the site (SEO/discovery) stays complete.
// Every indexable page renders a full <x-nexo-seo> head (description/canonical/
// OG/Twitter + hreflang), the private ones say noindex, and robots.txt /
// sitemap.xml keep serving with the right shape.
//
// The storefront and the directory used to emit a hand-written description and
// nothing else — no canonical, no OG, no hreflang — and their <title> silently
// fell back to the site name, because public-layout never declared `title` as a
// prop. That is the drift these tests exist to stop.
//
// Pest note: toContain() is variadic — a second argument is another needle, not a
// failure message — so human-readable messages go through toBeTrue()/toBe().

use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;

it('serves meta description, canonical, open graph and hreflang on the home page', function () {
    $html = $this->get('/')->assertOk()->getContent();

    expect($html)
        ->toContain('<meta name="description"')
        ->toContain('<link rel="canonical" href="'.url('/').'"')
        ->toContain('<meta property="og:title"')
        ->toContain('<meta property="og:url" content="'.url('/').'"')
        ->toContain('<meta property="og:image"')
        ->toContain('hreflang="es"')
        ->toContain('hreflang="en"')
        ->toContain('hreflang="pt"')
        ->toContain('hreflang="x-default"')
        // The component's doc comment must stay a comment (no leaked literal props)
        // and prop values must be escaped exactly once (no double-encoded entities).
        ->not->toContain(':hreflang=')
        ->not->toContain(':noindex=')
        ->not->toContain('&amp;#0');
});

it('emits the full seo head exactly once on every indexable page', function () {
    $business = Business::factory()->create(['slug' => 'seo-negocio', 'in_directory' => true]);
    Service::factory()->for($business)->create();

    $pages = [
        route('home'),
        route('directory'),
        route('directory.category', $business->category),
        route('public.business', $business),
        route('help'),
        route('legal.privacy'),
        route('legal.terms'),
    ];

    foreach ($pages as $url) {
        $html = (string) $this->get($url)->assertOk()->getContent();

        expect($html)->toContain('<link rel="canonical" href="'.$url.'"')
            ->and($html)->toContain('property="og:title"')
            ->and($html)->toContain('property="og:image"')
            ->and($html)->toContain('name="twitter:card"')
            ->and($html)->toContain('hreflang="x-default"');

        // Exactly one of each: the drift this component kills was a page adding
        // its own description on top of the shared block.
        expect(substr_count($html, 'name="description"'))->toBe(1, "duplicate description on {$url}");
        expect(substr_count($html, '<title>'))->toBe(1, "duplicate title on {$url}");
        expect(substr_count($html, 'name="theme-color"'))->toBe(1, "duplicate theme-color on {$url}");
    }
});

it('gives the storefront and the directory their own title and description', function () {
    $business = Business::factory()->create([
        'name' => 'Barbería Nexo',
        'slug' => 'barberia-nexo',
        'category' => 'barberia',
        'in_directory' => true,
        'brand_color' => '#123456',
    ]);
    Service::factory()->for($business)->create();

    $storefront = (string) $this->get(route('public.business', $business))->getContent();

    expect($storefront)->toContain('<title>Barbería Nexo — '.config('app.name').'</title>')
        ->and($storefront)->toContain('property="og:title" content="Barbería Nexo')
        ->and($storefront)->toContain('Reserva tu turno en Barbería Nexo')
        // The storefront paints the browser UI with the business accent, never
        // with the Nexo chrome violet.
        ->and($storefront)->toContain('<meta name="theme-color" content="#123456">');

    $category = (string) $this->get(route('directory.category', 'barberia'))->getContent();

    expect($category)->toContain('<title>'.__('nexo.categories.barberia'))
        ->and($category)->toContain('name="description" content="'.__('Encuentra dónde reservar en :category', ['category' => __('nexo.categories.barberia')]));
});

it('keeps the private and token-bearing pages out of the index', function () {
    $business = Business::factory()->create();
    $service = Service::factory()->for($business)->create();
    $professional = Professional::factory()->for($business)->create();
    $token = Booking::newManagementToken();

    Booking::factory()->for($business)->for($service)->for($professional)->create([
        'management_token' => $token['hash'],
    ]);

    $manage = (string) $this->get(route('booking.manage', $token['token']))->assertOk()->getContent();
    $dashboard = (string) $this->actingAs($business->user)->get(route('dashboard'))->assertOk()->getContent();

    foreach ([$manage, $dashboard] as $html) {
        expect($html)->toContain('name="robots" content="noindex')
            // Structured data on a private page is data leaving through the front door.
            ->and($html)->not->toContain('application/ld+json');
    }
});

it('serves robots.txt with the private surface disallowed and a sitemap pointer', function () {
    $response = $this->get('/robots.txt');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('text/plain');
    expect($response->getContent())
        ->toContain('User-agent: *')
        ->toContain('Disallow: /app')
        ->toContain('Disallow: /t/')
        ->toContain('Disallow: /login')
        ->toContain('Disallow: /register')
        ->toContain('Sitemap: '.url('/sitemap.xml'));
});

it('serves a valid sitemap.xml listing the public home page and the legal pages', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('xml');
    expect($response->getContent())
        ->toContain('<urlset')
        ->toContain('<loc>'.url('/').'</loc>')
        ->toContain('<loc>'.route('legal.privacy').'</loc>')
        ->toContain('<loc>'.route('legal.terms').'</loc>');

    expect(simplexml_load_string($response->getContent()))->not->toBeFalse();
});
