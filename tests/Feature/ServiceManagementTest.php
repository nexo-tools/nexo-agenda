<?php

use App\Models\Business;
use App\Models\Service;

beforeEach(function () {
    $this->business = Business::factory()->create();
    $this->owner = $this->business->user;
});

it('lists the business services', function () {
    Service::factory()->for($this->business)->create(['name' => 'Corte premium']);

    $this->actingAs($this->owner)
        ->get('/app/services')
        ->assertOk()
        ->assertSee('Corte premium');
});

it('creates a service', function () {
    $this->actingAs($this->owner)->post('/app/services', [
        'name' => 'Corte clásico',
        'duration_minutes' => 30,
        'price' => 8000,
        'mode' => 'in_person',
        'buffer_minutes' => 10,
        'min_notice_hours' => 2,
        'cancellation_hours' => 12,
        'max_advance_days' => 60,
        'is_active' => '1',
    ])->assertRedirect('/app/services');

    expect($this->business->services()->count())->toBe(1)
        ->and($this->business->services->first()->buffer_minutes)->toBe(10);
});

it('requires a video link for virtual services', function () {
    $this->actingAs($this->owner)->post('/app/services', [
        'name' => 'Asesoría online',
        'duration_minutes' => 30,
        'mode' => 'virtual',
        'buffer_minutes' => 0,
        'min_notice_hours' => 2,
        'cancellation_hours' => 12,
        'max_advance_days' => 60,
    ])->assertSessionHasErrors('video_link');
});

it('clears the video link when a service is in person', function () {
    $service = Service::factory()->for($this->business)->create([
        'mode' => 'virtual',
        'video_link' => 'https://meet.jit.si/sala',
    ]);

    $this->actingAs($this->owner)->put("/app/services/{$service->id}", [
        'name' => $service->name,
        'duration_minutes' => 30,
        'mode' => 'in_person',
        'video_link' => 'https://meet.jit.si/sala',
        'buffer_minutes' => 0,
        'min_notice_hours' => 2,
        'cancellation_hours' => 12,
        'max_advance_days' => 60,
        'is_active' => '1',
    ]);

    expect($service->refresh()->video_link)->toBeNull();
});

it('blocks editing another business service', function () {
    $foreign = Service::factory()->create();

    $this->actingAs($this->owner)->get("/app/services/{$foreign->id}/edit")->assertForbidden();
    $this->actingAs($this->owner)->delete("/app/services/{$foreign->id}")->assertForbidden();
});

it('deletes a service', function () {
    $service = Service::factory()->for($this->business)->create();

    $this->actingAs($this->owner)->delete("/app/services/{$service->id}")
        ->assertRedirect('/app/services');

    expect(Service::count())->toBe(0);
});
