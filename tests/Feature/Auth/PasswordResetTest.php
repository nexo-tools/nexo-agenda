<?php

use App\Models\User;
use App\Notifications\ResetPasswordQueued;
use Illuminate\Support\Facades\Notification;

it('sends a reset link to existing users', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPasswordQueued::class);
});

it('does not reveal whether an email exists', function () {
    Notification::fake();

    $this->post('/forgot-password', ['email' => 'nadie@example.com'])
        ->assertSessionHas('status');

    Notification::assertNothingSent();
});

it('resets the password with a valid token', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    $token = null;
    Notification::assertSentTo($user, ResetPasswordQueued::class, function ($notification) use (&$token) {
        $token = $notification->token;

        return true;
    });

    $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'nueva-clave-123',
        'password_confirmation' => 'nueva-clave-123',
    ])->assertRedirect('/login');

    $this->post('/login', ['email' => $user->email, 'password' => 'nueva-clave-123'])
        ->assertRedirect('/app');
});
