<?php

use App\Models\Booking;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Live bug (ecosystem-audit): guest-controlled fields (client name/email/phone/note)
// were written verbatim into the CSV exports. A value starting with = + - @ becomes
// an executable formula when the owner opens the file (CWE-1236). Neutralized by
// prefixing such cells with a single quote.

test('the bookings CSV export neutralizes formula injection in guest fields', function (): void {
    $user = User::factory()->has(Business::factory())->create();
    $business = $user->business;

    Booking::factory()->for($business)->create([
        'client_name' => '=HYPERLINK("http://evil.test","clic")',
        'client_phone' => '+542915550000',
        'note' => '@SUM(1+1)',
    ]);

    $response = $this->actingAs($user)->get(route('bookings.export'));
    $response->assertOk();

    $csv = $response->streamedContent();

    // The dangerous cells are prefixed with a single quote; never written as a formula.
    expect($csv)->toContain("'=HYPERLINK")
        ->and($csv)->toContain("'@SUM")
        ->and($csv)->not->toContain('"=HYPERLINK')  // raw formula would look like this
        ->and($csv)->not->toContain('"@SUM');
});

test('the clients CSV export neutralizes formula injection in guest fields', function (): void {
    $user = User::factory()->has(Business::factory())->create();
    $business = $user->business;

    Booking::factory()->for($business)->create([
        'client_name' => '+1234567890',
        'client_email' => 'ok@example.com',
    ]);

    $csv = $this->actingAs($user)->get(route('clients.export'))->streamedContent();

    // A phone-like value starting with + must not be treated as a formula.
    expect($csv)->toContain("'+1234567890");
});
