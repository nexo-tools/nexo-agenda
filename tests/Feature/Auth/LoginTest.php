<?php

use App\Models\User;

it('shows the login page', function () {
    $this->get('/login')->assertOk()->assertSee('Inicia sesión');
});

it('logs in with valid credentials', function () {
    $user = User::factory()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect('/app');

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create();

    $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'incorrecta'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logs out', function () {
    $this->actingAs(User::factory()->create())
        ->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});

it('redirects guests away from the dashboard', function () {
    $this->get('/app')->assertRedirect('/login');
});
