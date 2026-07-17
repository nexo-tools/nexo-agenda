<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;

beforeEach(function () {
    $this->business = Business::factory()->create(['slug' => 'checkin-test']);
    $this->owner = $this->business->user;
    $this->service = Service::factory()->for($this->business)->create();
    $this->professional = Professional::factory()->for($this->business)->create();

    $this->token = str_repeat('q', 48);
    $this->booking = Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_name' => 'Juan QR',
        'management_token' => hash('sha256', $this->token),
    ]);
});

it('shows a check-in qr on the client booking page', function () {
    $this->get("/t/{$this->token}")
        ->assertOk()
        ->assertSee('<svg', false)
        ->assertSee('Muestra este código al llegar');
});

it('shows the booking to the owner when scanning', function () {
    $this->actingAs($this->owner)
        ->get("/app/checkin/{$this->token}")
        ->assertOk()
        ->assertSee('Juan QR')
        ->assertSee('Marcar como Asistió');
});

it('marks the booking as attended', function () {
    $this->actingAs($this->owner)
        ->post("/app/checkin/{$this->token}")
        ->assertRedirect();

    expect($this->booking->refresh()->status)->toBe(BookingStatus::Attended);
});

it('does not change non-confirmed bookings', function () {
    $this->booking->update(['status' => BookingStatus::Cancelled]);

    $this->actingAs($this->owner)->post("/app/checkin/{$this->token}");

    expect($this->booking->refresh()->status)->toBe(BookingStatus::Cancelled);
});

it('blocks owners of other businesses', function () {
    $other = Business::factory()->create();

    $this->actingAs($other->user)
        ->get("/app/checkin/{$this->token}")
        ->assertForbidden();
});

it('requires login (the qr url is useless to the client)', function () {
    $this->get("/app/checkin/{$this->token}")->assertRedirect('/login');
});
