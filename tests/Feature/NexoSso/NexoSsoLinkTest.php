<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

require_once __DIR__.'/NexoSsoTestSupport.php';

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'nexo-sso.enabled' => true,
        'nexo-sso.issuer' => 'https://nexoid.test',
        'nexo-sso.client_id' => '11111111-2222-3333-4444-555555555555',
    ]);
});

test('AC-LINK-1: first SSO login with no local account creates and links a local user', function (): void {
    nexoSsoFakeProvider();

    nexoSsoCallback($this)->assertRedirect(route('dashboard'));

    $user = User::query()->where('email', 'user@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->nexo_id_sub)->toBe('user-uuid-0001')
        ->and($user->name)->toBe('Test User');
    $this->assertAuthenticatedAs($user);
});

// NOTE: don't call nexoSsoFakeProvider() twice in one test — Http::fake stacks
// stubs and the first registration keeps winning for the same URL.
test('AC-LINK-2: linking is refused when the provider has not verified the email', function (): void {
    $local = User::factory()->create(['email' => 'user@example.com']);

    nexoSsoFakeProvider([
        'access_token' => 'fake', 'token_type' => 'Bearer',
        'id_token' => nexoSsoIdToken(['email_verified' => false]),
    ]);

    nexoSsoCallback($this)->assertRedirect(route('login'))->assertSessionHasErrors('nexo_sso');
    $this->assertGuest();
    expect($local->fresh()->nexo_id_sub)->toBeNull();
});

test('AC-LINK-2: a provider-verified email links the existing local account', function (): void {
    $local = User::factory()->create(['email' => 'user@example.com']);

    nexoSsoFakeProvider();

    nexoSsoCallback($this)->assertRedirect(route('dashboard'));
    expect($local->fresh()->nexo_id_sub)->toBe('user-uuid-0001')
        ->and(User::query()->count())->toBe(1);
    $this->assertAuthenticatedAs($local->fresh());
});

test('AC-LINK-3: returning users match by sub even if their email changed on Nexo ID', function (): void {
    $linked = User::factory()->create(['email' => 'old@example.com']);
    $linked->forceFill(['nexo_id_sub' => 'user-uuid-0001'])->save();

    nexoSsoFakeProvider([
        'access_token' => 'fake', 'token_type' => 'Bearer',
        'id_token' => nexoSsoIdToken(['email' => 'brand-new@example.com']),
    ]);

    nexoSsoCallback($this)->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($linked->fresh());
    expect(User::query()->count())->toBe(1); // no duplicate account
});
