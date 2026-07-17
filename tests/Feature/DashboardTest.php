<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use Carbon\CarbonImmutable;

beforeEach(function () {
    CarbonImmutable::setTestNow('2026-07-20 12:00:00'); // Monday 09:00 in Buenos Aires

    $this->business = Business::factory()->create();
    $this->owner = $this->business->user;
    $this->service = Service::factory()->for($this->business)->create(['name' => 'Corte']);
    $this->professional = Professional::factory()->for($this->business)->create(['name' => 'Ana']);
});

afterEach(fn () => CarbonImmutable::setTestNow());

it('shows the day bookings grouped by professional', function () {
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_name' => 'Juan Cliente',
        'starts_at' => '2026-07-20 13:00:00', // 10:00 local
        'ends_at' => '2026-07-20 13:30:00',
    ]);

    $this->actingAs($this->owner)
        ->get('/app?date=2026-07-20')
        ->assertOk()
        ->assertSee('Ana')
        ->assertSee('10:00')
        ->assertSee('Juan Cliente')
        ->assertSee('Corte');
});

it('shows a week overview', function () {
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_name' => 'Cliente Semana',
        'starts_at' => '2026-07-22 13:00:00',
        'ends_at' => '2026-07-22 13:30:00',
    ]);

    $this->actingAs($this->owner)
        ->get('/app?date=2026-07-20&view=week')
        ->assertOk()
        ->assertSee('Cliente Semana');
});

it('creates a manual booking without email', function () {
    $this->actingAs($this->owner)->post('/app/bookings', [
        'service_id' => $this->service->id,
        'professional_id' => $this->professional->id,
        'date' => '2026-07-20',
        'time' => '16:00',
        'client_name' => 'Walk In',
    ])->assertRedirect('/app?date=2026-07-20');

    $booking = Booking::firstOrFail();
    expect($booking->client_email)->toBeNull()
        ->and($booking->starts_at->format('H:i'))->toBe('19:00'); // UTC
});

it('rejects a manual booking that collides', function () {
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'starts_at' => '2026-07-20 19:00:00',
        'ends_at' => '2026-07-20 19:30:00',
    ]);

    $this->actingAs($this->owner)->post('/app/bookings', [
        'service_id' => $this->service->id,
        'professional_id' => $this->professional->id,
        'date' => '2026-07-20',
        'time' => '16:15',
        'client_name' => 'Choque',
    ])->assertSessionHasErrors('time');

    expect(Booking::count())->toBe(1);
});

it('updates booking status', function () {
    $booking = Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
    ]);

    $this->actingAs($this->owner)
        ->patch("/app/bookings/{$booking->id}/status", ['status' => 'attended']);

    expect($booking->refresh()->status)->toBe(BookingStatus::Attended);
});

it('blocks status changes on other business bookings', function () {
    $foreign = Booking::factory()->create();

    $this->actingAs($this->owner)
        ->patch("/app/bookings/{$foreign->id}/status", ['status' => 'attended'])
        ->assertForbidden();
});

it('rejects services or professionals from another business', function () {
    $foreignService = Service::factory()->create();

    $this->actingAs($this->owner)->post('/app/bookings', [
        'service_id' => $foreignService->id,
        'professional_id' => $this->professional->id,
        'date' => '2026-07-20',
        'time' => '16:00',
        'client_name' => 'X',
    ])->assertSessionHasErrors('service_id');
});
