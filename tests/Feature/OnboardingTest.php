<?php

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// The integration blocker: a Nexo ID SSO sign-up creates a User but no Business
// (OIDC claims carry no category/city). Without a gate the whole /app 500s on
// $user->business->... — EnsureBusiness must redirect to onboarding instead.

test('an authenticated owner without a business is redirected from /app to onboarding', function (): void {
    $user = User::factory()->create(); // no business

    $this->actingAs($user)->get(route('dashboard'))
        ->assertRedirect(route('onboarding.create'));

    // A business-requiring inner route is gated too.
    $this->actingAs($user)->get(route('clients.index'))
        ->assertRedirect(route('onboarding.create'));
});

test('onboarding creates the business and the app becomes reachable', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('onboarding.store'), [
        'business_name' => 'Estudio Test',
        'category' => config('nexo.categories')[0],
        'city' => 'Buenos Aires',
    ])->assertRedirect(route('dashboard'));

    $business = $user->fresh()->business;
    expect($business)->not->toBeNull()
        ->and($business->name)->toBe('Estudio Test')
        ->and($business->slug)->not->toBeEmpty();

    $this->actingAs($user->fresh())->get(route('dashboard'))->assertOk();
});

test('onboarding rejects an invalid category and a missing city', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('onboarding.store'), [
        'business_name' => 'X',
        'category' => 'not-a-real-category',
    ])->assertSessionHasErrors(['category', 'city']);

    expect($user->fresh()->business)->toBeNull();
});

test('an owner who already has a business skips onboarding', function (): void {
    $user = User::factory()->has(Business::factory())->create();

    $this->actingAs($user)->get(route('onboarding.create'))
        ->assertRedirect(route('dashboard'));
});
