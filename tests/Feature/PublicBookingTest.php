<?php

use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use Carbon\CarbonImmutable;

beforeEach(function () {
    CarbonImmutable::setTestNow('2026-07-20 12:00:00'); // Monday 09:00 in Buenos Aires

    $this->business = Business::factory()->create(['slug' => 'barberia-test']);
    $this->service = Service::factory()->for($this->business)->create(['name' => 'Corte', 'duration_minutes' => 30]);
    $this->professional = Professional::factory()->for($this->business)->create(['name' => 'Ana']);
    $this->professional->scheduleBlocks()->create(['weekday' => 1, 'start_time' => '09:00', 'end_time' => '13:00']);
});

afterEach(fn () => CarbonImmutable::setTestNow());

it('shows the public business page with active services', function () {
    Service::factory()->for($this->business)->create(['name' => 'Oculto', 'is_active' => false]);

    $this->get('/barberia-test')
        ->assertOk()
        ->assertSee('Corte')
        ->assertDontSee('Oculto');
});

it('returns 404 for unknown businesses', function () {
    $this->get('/no-existe')->assertNotFound();
});

it('scopes services to the business in the url', function () {
    $foreign = Service::factory()->create();

    $this->get("/barberia-test/reservar/{$foreign->id}")->assertNotFound();
});

it('lists available times for a day', function () {
    $this->get("/barberia-test/reservar/{$this->service->id}/horarios?professional={$this->professional->id}&date=2026-07-27")
        ->assertOk()
        ->assertSee('09:00')
        ->assertSee('12:30')
        ->assertDontSee('13:00');
});

it('books a slot and redirects to the management link', function () {
    $response = $this->post("/barberia-test/reservar/{$this->service->id}", [
        'professional_id' => $this->professional->id,
        'start' => '2026-07-27 10:00',
        'client_name' => 'Juan Cliente',
        'client_email' => 'juan@example.com',
    ]);

    $booking = Booking::firstOrFail();

    // Stored in UTC (10:00 -03 = 13:00 UTC), token hashed in DB.
    expect($booking->starts_at->format('Y-m-d H:i'))->toBe('2026-07-27 13:00')
        ->and($booking->ends_at->format('H:i'))->toBe('13:30')
        ->and($booking->business_id)->toBe($this->business->id)
        ->and(strlen($booking->management_token))->toBe(64);

    $location = $response->headers->get('Location');
    expect($location)->toContain('/t/');

    $token = basename((string) $location);
    expect(hash('sha256', $token))->toBe($booking->management_token);
});

it('rejects a slot that was just taken', function () {
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
    ]);

    $this->post("/barberia-test/reservar/{$this->service->id}", [
        'professional_id' => $this->professional->id,
        'start' => '2026-07-27 10:00',
        'client_name' => 'Juan',
        'client_email' => 'juan@example.com',
    ])->assertRedirect()->assertSessionHas('slot_taken');

    expect(Booking::count())->toBe(1);
});

it('validates client data', function () {
    $this->post("/barberia-test/reservar/{$this->service->id}", [
        'professional_id' => $this->professional->id,
        'start' => '2026-07-27 10:00',
        'client_name' => '',
        'client_email' => 'no-es-email',
    ])->assertSessionHasErrors(['client_name', 'client_email']);
});

it('shows the booking on its management page', function () {
    $token = 'token-de-prueba-token-de-prueba-token-de-prueba9';
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_name' => 'Juan Cliente',
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
        'management_token' => hash('sha256', $token),
    ]);

    $this->get("/t/{$token}")
        ->assertOk()
        ->assertSee($this->business->name)
        ->assertSee('Corte')
        ->assertSee('10:00')
        ->assertSee('Confirmado');
});

it('returns 404 for an invalid management token', function () {
    $this->get('/t/token-invalido')->assertNotFound();
});
