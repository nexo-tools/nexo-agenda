<?php

use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    CarbonImmutable::setTestNow('2026-07-20 12:00:00');

    $this->business = Business::factory()->create(['slug' => 'stats-test']);
    $this->owner = $this->business->user;
    $this->service = Service::factory()->for($this->business)->create(['name' => 'Corte', 'duration_minutes' => 30]);
    $this->professional = Professional::factory()->for($this->business)->create();
});

afterEach(fn () => CarbonImmutable::setTestNow());

it('records one anonymous visit per visitor per day', function () {
    $this->get('/stats-test');
    $this->get('/stats-test');

    expect(DB::table('page_visits')->count())->toBe(1);

    $row = DB::table('page_visits')->first();
    expect($row->visitor_hash)->toHaveLength(64)
        ->and(json_encode($row))->not->toContain('127.0.0.1');
});

it('shows booking totals and rates for the period', function () {
    foreach ([
        ['status' => 'attended', 'starts_at' => '2026-07-18 13:00:00'],
        ['status' => 'attended', 'starts_at' => '2026-07-19 13:00:00'],
        ['status' => 'no_show', 'starts_at' => '2026-07-19 15:00:00'],
        ['status' => 'cancelled', 'starts_at' => '2026-07-19 17:00:00'],
        ['status' => 'confirmed', 'starts_at' => '2026-05-01 13:00:00'], // out of period
    ] as $attributes) {
        Booking::factory()->for($this->business)->create([
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'ends_at' => CarbonImmutable::parse($attributes['starts_at'])->addMinutes(30),
            ...$attributes,
        ]);
    }

    $response = $this->actingAs($this->owner)->get('/app/estadisticas');

    $response->assertOk()
        ->assertSee('Estadísticas')
        ->assertSee('Corte')
        ->assertSeeInOrder(['Turnos', '3'])
        ->assertSeeInOrder(['No-shows', '1'])
        ->assertSee('33%'); // no-show rate: 1 of 3 active
});

it('computes occupancy from schedule blocks', function () {
    // Mondays 4h available = 240 min; 30 min booked and attended = 13%.
    $this->professional->scheduleBlocks()->create(['weekday' => 1, 'start_time' => '09:00', 'end_time' => '13:00']);

    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'status' => 'attended',
        'starts_at' => '2026-07-20 13:00:00',
        'ends_at' => '2026-07-20 13:30:00',
    ]);

    $this->actingAs($this->owner)
        ->get('/app/estadisticas')
        ->assertOk()
        ->assertViewHas('stats', fn ($stats) => $stats['occupancy'] > 0 && $stats['occupancy'] <= 100);
});

it('does not leak other business data', function () {
    Booking::factory()->create(); // other business

    $this->actingAs($this->owner)
        ->get('/app/estadisticas')
        ->assertViewHas('stats', fn ($stats) => $stats['total'] === 0);
});
