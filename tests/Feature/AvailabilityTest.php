<?php

use App\Models\Absence;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use App\Services\Availability;
use Carbon\CarbonImmutable;

// Business timezone is America/Argentina/Buenos_Aires (UTC-3) via factory default.
// "Now" is fixed at 2026-07-20 12:00 UTC = 09:00 in Buenos Aires (a Monday).
beforeEach(function () {
    CarbonImmutable::setTestNow('2026-07-20 12:00:00');

    $this->business = Business::factory()->create();
    $this->service = Service::factory()->for($this->business)->create([
        'duration_minutes' => 30,
        'buffer_minutes' => 0,
        'min_notice_hours' => 0,
        'max_advance_days' => 60,
    ]);
    $this->professional = Professional::factory()->for($this->business)->create();
    // Monday 09:00-13:00, local time.
    $this->professional->scheduleBlocks()->create(['weekday' => 1, 'start_time' => '09:00', 'end_time' => '13:00']);

    $this->availability = new Availability;
    $this->nextMonday = CarbonImmutable::parse('2026-07-27', $this->business->timezone);
});

afterEach(fn () => CarbonImmutable::setTestNow());

it('offers slots every 15 minutes while the service fits in the block', function () {
    $slots = $this->availability->slots($this->service, $this->professional, $this->nextMonday);

    expect($slots)->toHaveCount(15)
        ->and($slots->first()->format('H:i'))->toBe('09:00')
        ->and($slots->last()->format('H:i'))->toBe('12:30');
});

it('returns nothing on days without schedule blocks', function () {
    $tuesday = $this->nextMonday->addDay();

    expect($this->availability->slots($this->service, $this->professional, $tuesday))->toBeEmpty();
});

it('excludes slots colliding with existing bookings', function () {
    // 10:00-10:30 local = 13:00-13:30 UTC.
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
    ]);

    $times = $this->availability->slots($this->service, $this->professional, $this->nextMonday)
        ->map(fn ($slot) => $slot->format('H:i'));

    expect($times)->not->toContain('09:45')
        ->not->toContain('10:00')
        ->not->toContain('10:15')
        ->toContain('09:30')
        ->toContain('10:30');
});

it('ignores cancelled bookings', function () {
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
        'status' => 'cancelled',
    ]);

    expect($this->availability->slots($this->service, $this->professional, $this->nextMonday))->toHaveCount(15);
});

it('adds the buffer of already-booked services', function () {
    $bufferedService = Service::factory()->for($this->business)->create(['buffer_minutes' => 15]);

    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $bufferedService->id,
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
    ]);

    $times = $this->availability->slots($this->service, $this->professional, $this->nextMonday)
        ->map(fn ($slot) => $slot->format('H:i'));

    // Buffer pushes the occupied interval to 10:45 local.
    expect($times)->not->toContain('10:30')->toContain('10:45');
});

it('respects the minimum notice on the current day', function () {
    $this->service->update(['min_notice_hours' => 2]);

    // Today is Monday 09:00 local; min notice 2h → first slot 11:00.
    $today = CarbonImmutable::parse('2026-07-20', $this->business->timezone);
    $slots = $this->availability->slots($this->service, $this->professional, $today);

    expect($slots->first()->format('H:i'))->toBe('11:00');
});

it('does not offer days beyond the booking horizon', function () {
    $this->service->update(['max_advance_days' => 5]);

    expect($this->availability->slots($this->service, $this->professional, $this->nextMonday))->toBeEmpty();
});

it('returns nothing during an absence', function () {
    $this->professional->absences()->create([
        'starts_on' => '2026-07-27',
        'ends_on' => '2026-07-28',
    ]);

    expect($this->availability->slots($this->service, $this->professional, $this->nextMonday))->toBeEmpty();
});

it('merges professionals when anyone can take the booking', function () {
    $other = Professional::factory()->for($this->business)->create();
    $other->scheduleBlocks()->create(['weekday' => 1, 'start_time' => '13:00', 'end_time' => '15:00']);

    $map = $this->availability->slotsForAny(
        $this->service,
        collect([$this->professional, $other]),
        $this->nextMonday
    );

    expect($map->get('09:00'))->toBe($this->professional->id)
        ->and($map->get('13:00'))->toBe($other->id)
        ->and($map->keys()->first())->toBe('09:00');
});

it('finds the first available day', function () {
    $this->professional->absences()->create([
        'starts_on' => '2026-07-20',
        'ends_on' => '2026-07-27',
    ]);

    $day = $this->availability->firstAvailableDay(
        $this->service,
        collect([$this->professional]),
        CarbonImmutable::now()
    );

    // Next working Monday after the absence.
    expect($day->toDateString())->toBe('2026-08-03');
});
