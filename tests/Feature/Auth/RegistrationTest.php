<?php

use App\Models\Business;

it('shows the registration page', function () {
    $this->get('/register')->assertOk()->assertSee('Crea tu cuenta');
});

it('registers a user with their business and logs them in', function () {
    $response = $this->post('/register', [
        'name' => 'Ana Dueña',
        'email' => 'ana@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'business_name' => 'Barbería Central',
        'category' => 'barberia',
        'city' => 'Buenos Aires',
    ]);

    $response->assertRedirect('/app');
    $this->assertAuthenticated();

    $business = Business::firstOrFail();
    expect($business->slug)->toBe('barberia-central')
        ->and($business->user->email)->toBe('ana@example.com');
});

it('never assigns a reserved slug', function () {
    $this->post('/register', [
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'business_name' => 'Admin',
        'category' => 'otro',
        'city' => 'Córdoba',
    ]);

    expect(Business::firstOrFail()->slug)->toBe('admin-2');
});

it('resolves slug collisions with a suffix', function () {
    Business::factory()->create(['slug' => 'barberia-central']);

    $this->post('/register', [
        'name' => 'Ana',
        'email' => 'ana2@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'business_name' => 'Barbería Central',
        'category' => 'barberia',
        'city' => 'Rosario',
    ]);

    expect(Business::where('slug', 'barberia-central-2')->exists())->toBeTrue();
});

it('rejects an unknown category', function () {
    $this->from('/register')->post('/register', [
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'business_name' => 'Negocio',
        'category' => 'inexistente',
        'city' => 'Salta',
    ])->assertSessionHasErrors('category');
});
