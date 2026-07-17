<?php

use App\Models\Business;

it('lists directory businesses in the sitemap', function () {
    $listed = Business::factory()->create(['slug' => 'listado', 'in_directory' => true]);
    $hidden = Business::factory()->create(['slug' => 'oculto', 'in_directory' => false]);

    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml');
    $response->assertSee(url('/'), false);
    $response->assertSee(route('directory'), false);
    $response->assertSee(route('public.business', $listed->slug), false);
    $response->assertDontSee(route('public.business', $hidden->slug), false);
});

it('serves a robots file pointing at the sitemap', function () {
    $response = $this->get('/robots.txt');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    $response->assertSee('Disallow: /app');
    $response->assertSee('Sitemap: '.url('/sitemap.xml'));
});
