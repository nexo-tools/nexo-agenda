<?php

use App\Models\Business;
use App\Models\Service;
use App\Services\PublicPageCache;
use Illuminate\Support\Facades\Cache;

it('caches the public business page data', function () {
    $business = Business::factory()->create();
    Service::factory()->for($business)->create(['is_active' => true]);

    $cache = app(PublicPageCache::class);
    $cache->businessPage($business);

    expect(Cache::has("public_page:business:{$business->id}"))->toBeTrue();
});

it('invalidates the cache when a service changes', function () {
    $business = Business::factory()->create();

    app(PublicPageCache::class)->businessPage($business);
    expect(Cache::has("public_page:business:{$business->id}"))->toBeTrue();

    Service::factory()->for($business)->create(['is_active' => true]);

    expect(Cache::has("public_page:business:{$business->id}"))->toBeFalse();
});

it('serves a fresh service list after invalidation', function () {
    $business = Business::factory()->create();

    // Prime the cache with an empty list.
    $this->get('/'.$business->slug)->assertOk()->assertDontSee('Corte clásico');

    Service::factory()->for($business)->create(['name' => 'Corte clásico', 'is_active' => true]);

    $this->get('/'.$business->slug)->assertOk()->assertSee('Corte clásico');
});
