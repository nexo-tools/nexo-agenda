<?php

use App\Models\Business;
use App\Models\Professional;

beforeEach(function () {
    $this->business = Business::factory()->create();
    $this->owner = $this->business->user;
});

it('creates a professional and redirects to its schedule', function () {
    $response = $this->actingAs($this->owner)
        ->post('/app/professionals', ['name' => 'Ana']);

    $professional = Professional::firstOrFail();
    $response->assertRedirect("/app/professionals/{$professional->id}/edit");
    expect($professional->business_id)->toBe($this->business->id);
});

it('saves a weekly schedule', function () {
    $professional = Professional::factory()->for($this->business)->create();

    $this->actingAs($this->owner)->put("/app/professionals/{$professional->id}", [
        'name' => $professional->name,
        'is_active' => '1',
        'blocks' => [
            1 => [['start' => '09:00', 'end' => '13:00'], ['start' => '14:00', 'end' => '19:00']],
            6 => [['start' => '10:00', 'end' => '14:00']],
        ],
    ])->assertRedirect('/app/professionals');

    expect($professional->scheduleBlocks()->count())->toBe(3)
        ->and($professional->scheduleBlocks()->where('weekday', 1)->count())->toBe(2);
});

it('rejects overlapping ranges on the same day', function () {
    $professional = Professional::factory()->for($this->business)->create();

    $this->actingAs($this->owner)->put("/app/professionals/{$professional->id}", [
        'name' => $professional->name,
        'blocks' => [
            1 => [['start' => '09:00', 'end' => '13:00'], ['start' => '12:00', 'end' => '18:00']],
        ],
    ])->assertSessionHasErrors('blocks.1');
});

it('rejects a range ending before it starts', function () {
    $professional = Professional::factory()->for($this->business)->create();

    $this->actingAs($this->owner)->put("/app/professionals/{$professional->id}", [
        'name' => $professional->name,
        'blocks' => [2 => [['start' => '15:00', 'end' => '09:00']]],
    ])->assertSessionHasErrors();
});

it('replaces the previous schedule on update', function () {
    $professional = Professional::factory()->for($this->business)->withWeekdaySchedule()->create();

    $this->actingAs($this->owner)->put("/app/professionals/{$professional->id}", [
        'name' => $professional->name,
        'blocks' => [3 => [['start' => '08:00', 'end' => '12:00']]],
    ]);

    expect($professional->scheduleBlocks()->count())->toBe(1);
});

it('registers and deletes absences', function () {
    $professional = Professional::factory()->for($this->business)->create();

    $this->actingAs($this->owner)->post("/app/professionals/{$professional->id}/absences", [
        'starts_on' => '2026-08-01',
        'ends_on' => '2026-08-07',
        'reason' => 'Vacaciones',
    ]);

    $absence = $professional->absences()->firstOrFail();

    $this->actingAs($this->owner)->delete("/app/absences/{$absence->id}");

    expect($professional->absences()->count())->toBe(0);
});

it('blocks managing another business professionals', function () {
    $foreign = Professional::factory()->create();

    $this->actingAs($this->owner)->get("/app/professionals/{$foreign->id}/edit")->assertForbidden();
    $this->actingAs($this->owner)
        ->post("/app/professionals/{$foreign->id}/absences", ['starts_on' => '2026-08-01', 'ends_on' => '2026-08-02'])
        ->assertForbidden();
});
