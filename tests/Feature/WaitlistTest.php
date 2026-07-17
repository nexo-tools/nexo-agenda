<?php

use App\Mail\WaitlistSlotFreed;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use App\Models\WaitlistEntry;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    CarbonImmutable::setTestNow('2026-07-20 12:00:00'); // Monday 09:00 in Buenos Aires
    Mail::fake();

    $this->business = Business::factory()->create(['slug' => 'espera-test']);
    $this->service = Service::factory()->for($this->business)->create();
    $this->professional = Professional::factory()->for($this->business)->create();
    $this->professional->scheduleBlocks()->create(['weekday' => 1, 'start_time' => '09:00', 'end_time' => '13:00']);
});

afterEach(fn () => CarbonImmutable::setTestNow());

function joinWaitlist(array $overrides = []): TestResponse
{
    return test()->post('/espera-test/reservar/'.test()->service->id.'/espera', [
        'professional' => 'any',
        'date' => '2026-07-27',
        'client_name' => 'Espera',
        'client_email' => 'espera@example.com',
        ...$overrides,
    ]);
}

it('adds a client to the waitlist', function () {
    joinWaitlist()->assertRedirect()->assertSessionHas('status');

    expect(WaitlistEntry::count())->toBe(1)
        ->and(WaitlistEntry::first()->professional_id)->toBeNull();
});

it('does not duplicate an entry for the same email and day', function () {
    joinWaitlist();
    joinWaitlist();

    expect(WaitlistEntry::count())->toBe(1);
});

it('rejects dates outside the booking horizon', function () {
    joinWaitlist(['date' => '2030-01-01'])->assertSessionHasErrors('date');

    expect(WaitlistEntry::count())->toBe(0);
});

it('notifies matching entries when a booking is cancelled', function () {
    joinWaitlist(); // any professional, 2026-07-27

    $token = str_repeat('c', 48);
    $booking = Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
        'management_token' => hash('sha256', $token),
    ]);

    $this->post("/t/{$token}/cancelar");

    Mail::assertSent(WaitlistSlotFreed::class, fn ($mail) => $mail->hasTo('espera@example.com'));
    expect(WaitlistEntry::first()->notified_at)->not->toBeNull();
});

it('does not notify entries for other days services or professionals', function () {
    joinWaitlist(['date' => '2026-07-28', 'client_email' => 'otro-dia@example.com']);

    $otherService = Service::factory()->for($this->business)->create();
    $this->post("/espera-test/reservar/{$otherService->id}/espera", [
        'professional' => 'any',
        'date' => '2026-07-27',
        'client_name' => 'Otro Servicio',
        'client_email' => 'otro-servicio@example.com',
    ]);

    $otherPro = Professional::factory()->for($this->business)->create();
    joinWaitlist(['professional' => (string) $otherPro->id, 'client_email' => 'otro-pro@example.com']);

    $token = str_repeat('d', 48);
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
        'management_token' => hash('sha256', $token),
    ]);

    $this->post("/t/{$token}/cancelar");

    Mail::assertNotSent(WaitlistSlotFreed::class);
});

it('notifies each entry only once', function () {
    joinWaitlist();

    foreach (['e', 'f'] as $char) {
        $token = str_repeat($char, 48);
        Booking::factory()->for($this->business)->create([
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'starts_at' => '2026-07-27 '.($char === 'e' ? '13:00:00' : '14:00:00'),
            'ends_at' => '2026-07-27 '.($char === 'e' ? '13:30:00' : '14:30:00'),
            'management_token' => hash('sha256', $token),
        ]);

        $this->post("/t/{$token}/cancelar");
    }

    Mail::assertSent(WaitlistSlotFreed::class, 1);
});

it('notifies when the owner cancels from the dashboard', function () {
    joinWaitlist();

    $booking = Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
    ]);

    $this->actingAs($this->business->user)
        ->patch("/app/bookings/{$booking->id}/status", ['status' => 'cancelled']);

    Mail::assertSent(WaitlistSlotFreed::class, fn ($mail) => $mail->hasTo('espera@example.com'));
});
