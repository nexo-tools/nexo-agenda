<?php

use App\Mail\BookingChangedByClient;
use App\Mail\WaitlistJoined;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Mail;

/**
 * The owner's side of the client-managed flows, and the receipt for joining a
 * waitlist: the three mails this app owed and never sent.
 */
beforeEach(function () {
    CarbonImmutable::setTestNow('2026-07-20 12:00:00');
    Mail::fake();

    $this->business = Business::factory()->create(['slug' => 'avisos', 'name' => 'Estudio Avisos']);
    $this->service = Service::factory()->for($this->business)->create(['duration_minutes' => 30, 'cancellation_hours' => 2]);
    $this->professional = Professional::factory()->for($this->business)->create();
    $this->professional->scheduleBlocks()->create(['weekday' => 1, 'start_time' => '09:00', 'end_time' => '13:00']);
});

function bookingWithToken(): array
{
    $token = Booking::newManagementToken();

    $booking = test()->business->bookings()->create([
        'professional_id' => test()->professional->id,
        'service_id' => test()->service->id,
        'client_name' => 'Juan',
        'client_email' => 'juan@example.com',
        'client_phone' => '+541100000000',
        'locale' => 'pt',
        'starts_at' => CarbonImmutable::parse('2026-07-27 13:00:00'),
        'ends_at' => CarbonImmutable::parse('2026-07-27 13:30:00'),
        'management_token' => $token['hash'],
    ]);

    return [$booking, $token['token']];
}

it('tells the owner when a client cancels from their management link', function () {
    [$booking, $token] = bookingWithToken();

    $this->post(route('booking.cancel', $token))->assertRedirect();

    // The owner has a hole in their day now, and used to learn about it by
    // opening the dashboard.
    Mail::assertQueued(
        BookingChangedByClient::class,
        fn (BookingChangedByClient $mail): bool => $mail->hasTo($this->business->user->email) && $mail->cancelled === true
    );
});

it('tells the owner when a client reschedules from their management link', function () {
    [$booking, $token] = bookingWithToken();

    $this->post(route('booking.reschedule.update', $token), [
        'start' => '2026-07-27 11:00',
    ])->assertRedirect();

    Mail::assertQueued(
        BookingChangedByClient::class,
        fn (BookingChangedByClient $mail): bool => $mail->hasTo($this->business->user->email) && $mail->cancelled === false
    );
});

it('writes the owner notice in the instance language, not the client\'s', function () {
    [$booking, $token] = bookingWithToken();

    // The booking was made in pt; the owner reads their instance's language.
    $this->post(route('booking.cancel', $token));

    Mail::assertQueued(
        BookingChangedByClient::class,
        fn (BookingChangedByClient $mail): bool => $mail->locale === config('app.locale')
    );
});

it('confirms to a person that they are on the waitlist', function () {
    $this->post(route('public.waitlist', [$this->business, $this->service]), [
        'professional' => 'any',
        'date' => '2026-07-27',
        'client_name' => 'Ana',
        'client_email' => 'ana@example.com',
    ])->assertRedirect();

    // Joining used to leave nothing behind but a flash message.
    Mail::assertQueued(WaitlistJoined::class, fn (WaitlistJoined $mail): bool => $mail->hasTo('ana@example.com'));
});

it('does not confirm twice when somebody joins the same list again', function () {
    $payload = [
        'professional' => 'any',
        'date' => '2026-07-27',
        'client_name' => 'Ana',
        'client_email' => 'ana@example.com',
    ];

    $this->post(route('public.waitlist', [$this->business, $this->service]), $payload);
    $this->post(route('public.waitlist', [$this->business, $this->service]), $payload);

    Mail::assertQueuedCount(1);
});
