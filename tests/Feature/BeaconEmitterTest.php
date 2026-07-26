<?php

use App\Models\Business;

// The cookieless beacon emitter is wired into the owner-facing chrome (landing,
// dashboard, auth, help) only. It fires when this instance opts in
// (NEXO_BEACON_ENABLED) and respects Do Not Track. The public business
// storefront never emits it — a business's customers are not measured by the hub.

it('renders the beacon metas on the landing only when the beacon is enabled', function () {
    config(['nexo.beacon.enabled' => true]);

    $this->get('/')
        ->assertSee('name="nexo:beacon-endpoint"', false)
        ->assertSee('name="nexo:beacon-origin" content="nexoagenda"', false);
});

it('renders no beacon metas when the beacon is off (default/standalone)', function () {
    config(['nexo.beacon.enabled' => false]);

    $this->get('/')
        ->assertDontSee('nexo:beacon-endpoint', false)
        ->assertDontSee('nexo:beacon-origin', false);
});

it('never emits the beacon on the public business storefront, even when enabled', function () {
    config(['nexo.beacon.enabled' => true]);
    $business = Business::factory()->create();

    $this->get('/'.$business->slug)
        ->assertOk()
        ->assertDontSee('nexo:beacon-endpoint', false)
        ->assertDontSee('nexo:beacon-origin', false);
});

it('ships the shareable snippet in the app bundle and honours Do Not Track', function () {
    $source = file_get_contents(resource_path('js/nexo-beacon.js'));

    expect($source)
        ->toContain('doNotTrack')
        ->toContain('globalPrivacyControl')
        ->toContain('sendBeacon')
        ->toContain("event: 'pageview'");

    expect(file_get_contents(resource_path('js/app.js')))->toContain('nexo-beacon.js');
});
