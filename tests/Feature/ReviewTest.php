<?php

use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Review;
use App\Models\Service;

beforeEach(function () {
    $this->business = Business::factory()->create(['slug' => 'resenas-test']);
    $this->owner = $this->business->user;
    $this->service = Service::factory()->for($this->business)->create();
    $this->professional = Professional::factory()->for($this->business)->create();

    $this->token = str_repeat('r', 48);
    $this->booking = Booking::factory()->for($this->business)->create([
        'professional_id' => $this->professional->id,
        'service_id' => $this->service->id,
        'client_name' => 'Juan Reseña',
        'status' => 'attended',
        'management_token' => hash('sha256', $this->token),
    ]);
});

it('lets an attended booking leave one review', function () {
    $this->post("/t/{$this->token}/resena", ['rating' => 5, 'comment' => 'Excelente atención'])
        ->assertRedirect("/t/{$this->token}");

    $review = Review::firstOrFail();
    expect($review->rating)->toBe(5)
        ->and($review->business_id)->toBe($this->business->id)
        ->and($review->client_name)->toBe('Juan Reseña');

    // Second attempt is ignored.
    $this->post("/t/{$this->token}/resena", ['rating' => 1]);
    expect(Review::count())->toBe(1);
});

it('rejects reviews from non-attended bookings', function () {
    $this->booking->update(['status' => 'confirmed']);

    $this->post("/t/{$this->token}/resena", ['rating' => 4]);

    expect(Review::count())->toBe(0);
});

it('validates the rating range', function () {
    $this->post("/t/{$this->token}/resena", ['rating' => 9])->assertSessionHasErrors('rating');
});

it('shows the average and comments on the public page', function () {
    $this->post("/t/{$this->token}/resena", ['rating' => 4, 'comment' => 'Muy buena onda']);

    $this->get('/resenas-test')
        ->assertOk()
        ->assertSee('4,0')
        ->assertSee('Muy buena onda')
        ->assertSee('Juan Reseña');
});

it('hides moderated reviews from the public page', function () {
    $this->post("/t/{$this->token}/resena", ['rating' => 1, 'comment' => 'Comentario ofensivo']);
    $review = Review::firstOrFail();

    $this->actingAs($this->owner)->patch("/app/resenas/{$review->id}");

    $this->get('/resenas-test')->assertDontSee('Comentario ofensivo');
});

it('blocks moderating another business review', function () {
    $this->post("/t/{$this->token}/resena", ['rating' => 3]);
    $review = Review::firstOrFail();

    $other = Business::factory()->create();
    $this->actingAs($other->user)->patch("/app/resenas/{$review->id}")->assertForbidden();
});

it('shows the review form only after attendance', function () {
    $this->get("/t/{$this->token}")->assertSee('¿Cómo estuvo tu experiencia?');

    $this->post("/t/{$this->token}/resena", ['rating' => 5]);

    $this->get("/t/{$this->token}")
        ->assertDontSee('¿Cómo estuvo tu experiencia?')
        ->assertSee('¡Gracias', false);
});
