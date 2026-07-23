<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

require_once __DIR__.'/NexoSsoTestSupport.php';

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'nexo-sso.enabled' => true,
        'nexo-sso.issuer' => 'https://nexoid.test',
        'nexo-sso.client_id' => '11111111-2222-3333-4444-555555555555',
    ]);
});

test('AC-DEGRADE-1: with the provider unreachable, active sessions keep working', function (): void {
    Route::middleware(['web', 'auth'])->get('/nexo-sso/probe', fn () => 'ok');
    Http::fake(fn () => throw new ConnectionException('provider down'));

    $this->actingAs(User::factory()->create())
        ->get('/nexo-sso/probe')
        ->assertOk();
});

test('AC-DEGRADE-2: with the provider unreachable, starting a login fails gracefully, never a 500', function (): void {
    Http::fake(fn () => throw new ConnectionException('provider down'));

    $this->get(route('nexo-sso.redirect'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('nexo_sso');
    $this->assertGuest();
});

test('AC-DEGRADE-2: a provider that dies mid-flow aborts the callback with a safe error', function (): void {
    Http::fake(fn () => throw new ConnectionException('provider down'));

    nexoSsoCallback($this)
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('nexo_sso');
    $this->assertGuest();
});
