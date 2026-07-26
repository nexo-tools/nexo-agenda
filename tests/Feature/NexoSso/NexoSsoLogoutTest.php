<?php

// RP-initiated (central) logout: the tool ends its own session AND hands the
// browser to the provider's end_session_endpoint so the Nexo ID session ends
// too. The provider validates post_logout_redirect_uri against the id_token_hint
// client's registered URIs (see the Nexo ID end_session controller) — so the
// client just needs to send a well-formed request and degrade safely.

use Illuminate\Foundation\Testing\RefreshDatabase;

require_once __DIR__.'/NexoSsoTestSupport.php';

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'nexo-sso.enabled' => true,
        'nexo-sso.issuer' => 'https://nexoid.test',
        'nexo-sso.client_id' => '11111111-2222-3333-4444-555555555555',
        'nexo-sso.post_logout_redirect_uri' => 'https://client.test/after-logout',
    ]);
});

test('AC-LOGOUT-1: a successful login stores the raw id_token for the logout hint', function (): void {
    $token = nexoSsoIdToken();
    nexoSsoFakeProvider(['access_token' => 'fake', 'token_type' => 'Bearer', 'id_token' => $token]);

    nexoSsoCallback($this);

    expect(session('nexo_sso.id_token'))->toBe($token);
});

test('AC-LOGOUT-1: logout ends the tool session and redirects to end_session with hint + post_logout_redirect_uri', function (): void {
    nexoSsoFakeProvider();
    nexoSsoCallback($this);
    $this->assertAuthenticated();

    $response = $this->post(route('nexo-sso.logout'));

    $response->assertRedirect();
    $location = (string) $response->headers->get('Location');
    expect($location)->toStartWith('https://nexoid.test/oauth/logout?');

    parse_str((string) parse_url($location, PHP_URL_QUERY), $query);
    expect($query)->toHaveKey('id_token_hint')
        ->and($query['id_token_hint'])->not->toBe('')
        ->and($query['post_logout_redirect_uri'])->toBe('https://client.test/after-logout');

    $this->assertGuest(); // the local session is gone regardless of the provider round-trip
});

test('AC-LOGOUT-1: with no end_session_endpoint advertised, logout stays local (no open redirect)', function (): void {
    nexoSsoFakeProvider(null, ['end_session_endpoint' => null]);
    nexoSsoCallback($this);
    $this->assertAuthenticated();

    $this->post(route('nexo-sso.logout'))->assertRedirect(route('login'));
    $this->assertGuest();
});
