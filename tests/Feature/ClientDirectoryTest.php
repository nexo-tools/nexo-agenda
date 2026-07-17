<?php

use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;

beforeEach(function () {
    $this->business = Business::factory()->create();
    $this->owner = $this->business->user;
    $this->service = Service::factory()->for($this->business)->create(['name' => 'Corte']);
    $this->professional = Professional::factory()->for($this->business)->create();
});

function makeBooking(array $attributes): Booking
{
    return Booking::factory()->for(test()->business)->create([
        'professional_id' => test()->professional->id,
        'service_id' => test()->service->id,
        ...$attributes,
    ]);
}

it('aggregates bookings into clients by email', function () {
    makeBooking(['client_name' => 'Juan', 'client_email' => 'juan@example.com', 'status' => 'attended']);
    makeBooking(['client_name' => 'Juan', 'client_email' => 'juan@example.com', 'status' => 'no_show']);
    makeBooking(['client_name' => 'Ana', 'client_email' => 'ana@example.com', 'status' => 'confirmed']);

    $this->actingAs($this->owner)
        ->get('/app/clients')
        ->assertOk()
        ->assertSee('Juan')
        ->assertSee('Ana')
        ->assertSee('2 turnos')
        ->assertSee('1 asistidos');
});

it('searches clients', function () {
    makeBooking(['client_name' => 'Juan', 'client_email' => 'juan@example.com']);
    makeBooking(['client_name' => 'Ana', 'client_email' => 'ana@example.com']);

    $this->actingAs($this->owner)
        ->get('/app/clients?q=juan')
        ->assertSee('Juan')
        ->assertDontSee('Ana');
});

it('shows a client history scoped to the business', function () {
    makeBooking(['client_name' => 'Juan', 'client_email' => 'juan@example.com', 'status' => 'attended']);

    $this->actingAs($this->owner)
        ->get('/app/clients/detail?key=juan@example.com')
        ->assertOk()
        ->assertSee('Juan')
        ->assertSee('Corte')
        ->assertSee('Asistió');

    // Another business cannot see this client.
    $other = Business::factory()->create();
    $this->actingAs($other->user)
        ->get('/app/clients/detail?key=juan@example.com')
        ->assertNotFound();
});

it('never mixes clients across businesses', function () {
    makeBooking(['client_name' => 'Juan', 'client_email' => 'juan@example.com']);
    Booking::factory()->create(['client_name' => 'Extraño', 'client_email' => 'otro@example.com']);

    $this->actingAs($this->owner)
        ->get('/app/clients')
        ->assertSee('Juan')
        ->assertDontSee('Extraño');
});

it('exports clients as csv', function () {
    makeBooking(['client_name' => 'Juan', 'client_email' => 'juan@example.com', 'status' => 'attended']);

    $response = $this->actingAs($this->owner)->get('/app/clients/export.csv');

    $response->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');

    $csv = $response->streamedContent();
    expect($csv)->toContain('nombre,email')
        ->toContain('Juan,juan@example.com');
});

it('exports bookings as csv', function () {
    makeBooking([
        'client_name' => 'Juan',
        'client_email' => 'juan@example.com',
        'starts_at' => '2026-07-27 13:00:00',
        'ends_at' => '2026-07-27 13:30:00',
    ]);

    $response = $this->actingAs($this->owner)->get('/app/bookings/export.csv');

    $csv = $response->streamedContent();
    // 13:00 UTC = 10:00 in the business timezone.
    expect($csv)->toContain('fecha,servicio')
        ->toContain('"2026-07-27 10:00",Corte')
        ->toContain('confirmed');
});
