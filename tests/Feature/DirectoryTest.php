<?php

use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;

beforeEach(function () {
    $this->listed = Business::factory()->create([
        'name' => 'Barbería Visible',
        'slug' => 'barberia-visible',
        'category' => 'barberia',
        'city' => 'Buenos Aires',
        'in_directory' => true,
    ]);

    Business::factory()->create([
        'name' => 'Negocio Privado',
        'in_directory' => false,
    ]);
});

it('lists only opted-in businesses', function () {
    $this->get('/explorar')
        ->assertOk()
        ->assertSee('Barbería Visible')
        ->assertDontSee('Negocio Privado');
});

it('filters by category via the seo url', function () {
    Business::factory()->create([
        'name' => 'Spa Zen',
        'category' => 'spa',
        'in_directory' => true,
    ]);

    $this->get('/explorar/rubro/barberia')
        ->assertOk()
        ->assertSee('Barbería Visible')
        ->assertDontSee('Spa Zen');

    $this->get('/explorar/rubro/no-existe')->assertNotFound();
});

it('filters by city and name', function () {
    Business::factory()->create([
        'name' => 'Cortes Rosario',
        'category' => 'barberia',
        'city' => 'Rosario',
        'in_directory' => true,
    ]);

    $this->get('/explorar?ciudad=Rosario')
        ->assertSee('Cortes Rosario')
        ->assertDontSee('Barbería Visible');

    $this->get('/explorar?q=Visible')
        ->assertSee('Barbería Visible')
        ->assertDontSee('Cortes Rosario');
});

it('shows the average rating', function () {
    $service = Service::factory()->for($this->listed)->create();
    $professional = Professional::factory()->for($this->listed)->create();
    $booking = Booking::factory()->for($this->listed)->create([
        'professional_id' => $professional->id,
        'service_id' => $service->id,
        'status' => 'attended',
    ]);
    $this->listed->reviews()->create([
        'booking_id' => $booking->id,
        'rating' => 4,
        'client_name' => 'Juan',
    ]);

    $this->get('/explorar')->assertSee('4,0');
});

it('opts a business in from settings', function () {
    $business = Business::factory()->create(['in_directory' => false]);

    $this->actingAs($business->user)->put('/app/settings', [
        'name' => $business->name,
        'category' => $business->category,
        'city' => $business->city,
        'in_directory' => '1',
    ]);

    expect($business->refresh()->in_directory)->toBeTrue();
});
