<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use Carbon\CarbonImmutable;

beforeEach(function () {
    CarbonImmutable::setTestNow('2026-07-20 12:00:00'); // Monday 09:00 in Buenos Aires

    $this->business = Business::factory()->create(['slug' => 'estudio-test']);
    $this->service = Service::factory()->for($this->business)->create([
        'duration_minutes' => 30,
        'cancellation_hours' => 12,
    ]);
    $this->professional = Professional::factory()->for($this->business)->create();
    $this->professional->scheduleBlocks()->create(['weekday' => 1, 'start_time' => '09:00', 'end_time' => '13:00']);

    $this->token = 'token-de-prueba-token-de-prueba-token-de-prueba9';
    // Next Monday 10:00 local = 13:00 UTC.
    $this->booking = Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
        'management_token' => hash('sha256', $this->token),
    ]);
});

afterEach(fn () => CarbonImmutable::setTestNow());

it('cancels a booking within the window', function () {
    $this->post("/t/{$this->token}/cancelar")
        ->assertRedirect("/t/{$this->token}");

    expect($this->booking->refresh()->status)->toBe(BookingStatus::Cancelled)
        ->and($this->booking->cancelled_at)->not->toBeNull();
});

it('refuses to cancel after the deadline', function () {
    // 6h before the booking, with a 12h window.
    CarbonImmutable::setTestNow('2026-07-27 07:00:00');

    $this->post("/t/{$this->token}/cancelar");

    expect($this->booking->refresh()->status)->toBe(BookingStatus::Confirmed);
});

it('reschedules to a free slot', function () {
    $this->post("/t/{$this->token}/reprogramar", ['start' => '2026-07-27 11:00'])
        ->assertRedirect("/t/{$this->token}");

    expect($this->booking->refresh()->starts_at->format('Y-m-d H:i'))->toBe('2026-07-27 14:00');
});

it('allows rescheduling to a slot overlapping itself', function () {
    // 10:15 overlaps the current 10:00-10:30 booking — must not self-block.
    $this->post("/t/{$this->token}/reprogramar", ['start' => '2026-07-27 10:15']);

    expect($this->booking->refresh()->starts_at->format('H:i'))->toBe('13:15');
});

it('rejects rescheduling into an occupied slot', function () {
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'starts_at' => '2026-07-27 14:00:00',
        'ends_at' => '2026-07-27 14:30:00',
    ]);

    $this->post("/t/{$this->token}/reprogramar", ['start' => '2026-07-27 11:00'])
        ->assertSessionHas('slot_taken');

    expect($this->booking->refresh()->starts_at->format('H:i'))->toBe('13:00');
});

it('shows management actions while the window is open', function () {
    $this->get("/t/{$this->token}")
        ->assertSee('Reprogramar')
        ->assertSee('Cancelar turno');
});

it('hides management actions after the deadline', function () {
    CarbonImmutable::setTestNow('2026-07-27 07:00:00');

    $this->get("/t/{$this->token}")
        ->assertDontSee('Cancelar turno')
        ->assertSee('El plazo para cancelar');
});

it('frees the slot after cancelling', function () {
    $this->post("/t/{$this->token}/cancelar");

    $this->get("/estudio-test/reservar/{$this->service->id}/horarios?professional={$this->professional->id}&date=2026-07-27")
        ->assertSee('10:00');
});
