<?php

use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use Carbon\CarbonImmutable;

beforeEach(function () {
    CarbonImmutable::setTestNow('2026-07-20 14:00:00'); // Monday 11:00 in Buenos Aires

    $this->business = Business::factory()->create();
    $this->owner = $this->business->user;
    $this->service = Service::factory()->for($this->business)->create(['name' => 'Corte']);
    $this->professional = Professional::factory()->for($this->business)->create(['name' => 'Ana']);
});

afterEach(fn () => CarbonImmutable::setTestNow());

it('requires authentication', function () {
    $this->get('/app/mostrador')->assertRedirect('/login');
});

it('shows only today with the next booking highlighted', function () {
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_name' => 'Pasado',
        'starts_at' => '2026-07-20 12:00:00', // 09:00 local, already over
        'ends_at' => '2026-07-20 12:30:00',
    ]);

    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_name' => 'Siguiente',
        'starts_at' => '2026-07-20 15:00:00', // 12:00 local, upcoming
        'ends_at' => '2026-07-20 15:30:00',
    ]);

    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_name' => 'Otro Día',
        'starts_at' => '2026-07-21 15:00:00',
        'ends_at' => '2026-07-21 15:30:00',
    ]);

    $response = $this->actingAs($this->owner)->get('/app/mostrador');

    $response->assertOk()
        ->assertSee('Pasado')
        ->assertSee('Siguiente')
        ->assertSee('Próximo')
        ->assertDontSee('Otro Día')
        ->assertSee('http-equiv="refresh"', false);
});

it('hides cancelled bookings', function () {
    Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_name' => 'Cancelado Hoy',
        'starts_at' => '2026-07-20 15:00:00',
        'ends_at' => '2026-07-20 15:30:00',
        'status' => 'cancelled',
    ]);

    $this->actingAs($this->owner)->get('/app/mostrador')->assertDontSee('Cancelado Hoy');
});
