<?php

use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;

beforeEach(function () {
    $this->business = Business::factory()->create();
    $this->service = Service::factory()->for($this->business)->create(['name' => 'Corte']);
    $this->professional = Professional::factory()->for($this->business)->create();
});

it('assigns a feed token on creation', function () {
    expect($this->professional->feed_token)->not->toBeNull()
        ->and(strlen($this->professional->feed_token))->toBe(48);
});

it('serves the professional bookings as an ics feed', function () {
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_name' => 'Juan',
        'starts_at' => now()->addDays(2)->setTime(13, 0),
        'ends_at' => now()->addDays(2)->setTime(13, 30),
    ]);

    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_name' => 'Cancelado',
        'status' => 'cancelled',
    ]);

    $response = $this->get("/feeds/{$this->professional->feed_token}.ics");

    $response->assertOk()->assertHeader('content-type', 'text/calendar; charset=UTF-8');

    $ics = $response->getContent();
    expect($ics)->toContain('Juan — Corte')
        ->toContain('X-WR-CALNAME:'.$this->professional->name)
        ->not->toContain('Cancelado');
});

it('does not include other professionals bookings', function () {
    $other = Professional::factory()->for($this->business)->create();
    Booking::factory()->for($this->business)->create([
        'professional_id' => $other->id,
        'service_id' => $this->service->id,
        'client_name' => 'Ajeno',
    ]);

    $ics = $this->get("/feeds/{$this->professional->feed_token}.ics")->getContent();

    expect($ics)->not->toContain('Ajeno');
});

it('returns 404 for an unknown token', function () {
    $this->get('/feeds/token-inexistente.ics')->assertNotFound();
});

it('regenerates the token and kills the old link', function () {
    $old = $this->professional->feed_token;

    $this->actingAs($this->business->user)
        ->post("/app/professionals/{$this->professional->id}/feed-token")
        ->assertRedirect();

    expect($this->professional->refresh()->feed_token)->not->toBe($old);
    $this->get("/feeds/{$old}.ics")->assertNotFound();
});

it('blocks regenerating another business feed token', function () {
    $foreign = Professional::factory()->create();

    $this->actingAs($this->business->user)
        ->post("/app/professionals/{$foreign->id}/feed-token")
        ->assertForbidden();
});
