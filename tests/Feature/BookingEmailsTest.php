<?php

use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmed;
use App\Mail\BookingReminder;
use App\Mail\BookingRescheduled;
use App\Mail\NewBookingReceived;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use App\Services\IcsFile;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    CarbonImmutable::setTestNow('2026-07-20 12:00:00'); // Monday 09:00 in Buenos Aires
    Mail::fake();

    $this->business = Business::factory()->create(['slug' => 'mail-test', 'name' => 'Estudio Prueba']);
    $this->service = Service::factory()->for($this->business)->create(['duration_minutes' => 30]);
    $this->professional = Professional::factory()->for($this->business)->create();
    $this->professional->scheduleBlocks()->create(['weekday' => 1, 'start_time' => '09:00', 'end_time' => '13:00']);
});

afterEach(fn () => CarbonImmutable::setTestNow());

it('emails the client and the owner on a new public booking', function () {
    $response = $this->post("/mail-test/reservar/{$this->service->id}", [
        'professional_id' => $this->professional->id,
        'start' => '2026-07-27 10:00',
        'client_name' => 'Juan',
        'client_email' => 'juan@example.com',
    ]);

    $response->assertSessionHasNoErrors();
    expect(Booking::count())->toBe(1);

    Mail::assertSent(BookingConfirmed::class, fn ($mail) => $mail->hasTo('juan@example.com'));
    Mail::assertSent(NewBookingReceived::class, fn ($mail) => $mail->hasTo($this->business->user->email));
});

it('emails the client when they cancel', function () {
    $token = str_repeat('a', 48);
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_email' => 'juan@example.com',
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
        'management_token' => hash('sha256', $token),
    ]);

    $this->post("/t/{$token}/cancelar");

    Mail::assertSent(BookingCancelled::class, fn ($mail) => $mail->hasTo('juan@example.com'));
});

it('emails the client when they reschedule', function () {
    $token = str_repeat('b', 48);
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_email' => 'juan@example.com',
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
        'management_token' => hash('sha256', $token),
    ]);

    $this->post("/t/{$token}/reprogramar", ['start' => '2026-07-27 11:00']);

    Mail::assertSent(BookingRescheduled::class, fn ($mail) => $mail->hasTo('juan@example.com'));
});

it('sends reminders once for bookings within 24 hours', function () {
    $soon = Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_email' => 'pronto@example.com',
        'starts_at' => '2026-07-21 11:00:00',
        'ends_at' => '2026-07-21 11:30:00',
    ]);

    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_email' => 'lejos@example.com',
        'starts_at' => '2026-07-25 11:00:00',
        'ends_at' => '2026-07-25 11:30:00',
    ]);

    $this->artisan('nexo:send-reminders')->assertSuccessful();

    Mail::assertSent(BookingReminder::class, 1);
    Mail::assertSent(BookingReminder::class, fn ($mail) => $mail->hasTo('pronto@example.com'));
    expect($soon->refresh()->reminded_at)->not->toBeNull();

    // Second run must not resend.
    $this->artisan('nexo:send-reminders');
    Mail::assertSent(BookingReminder::class, 1);
});

it('generates a valid ics file', function () {
    $booking = Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
    ]);

    $ics = (new IcsFile)->forBooking($booking->load(['business', 'service', 'professional']));

    expect($ics)->toContain('BEGIN:VCALENDAR')
        ->toContain('DTSTART:20260727T130000Z')
        ->toContain('DTEND:20260727T133000Z')
        ->toContain($this->business->name)
        ->toContain('END:VCALENDAR');
});
