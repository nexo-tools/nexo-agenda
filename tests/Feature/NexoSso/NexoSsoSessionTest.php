<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
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
    Route::middleware(['web', 'auth'])->get('/nexo-sso/probe', fn () => 'ok');
});

test('AC-SESS-1: a successful callback establishes a tool-owned session that needs no further provider calls', function (): void {
    nexoSsoFakeProvider();
    nexoSsoCallback($this)->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    // From here on the provider is gone — the local session alone suffices.
    Http::fake(fn () => throw new ConnectionException('provider down'));
    $this->get('/nexo-sso/probe')->assertOk();
});

test('AC-SESS-2: local logout ends only the tool session and never calls the provider', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);
    Http::fake();

    Auth::logout();

    $this->assertGuest();
    Http::assertNothingSent(); // central logout is the provider's own concern
});
