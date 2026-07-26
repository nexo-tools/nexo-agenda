<?php

use App\Http\Middleware\NexoSsoSilentLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

require_once __DIR__.'/NexoSsoTestSupport.php';

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'nexo-sso.enabled' => true,
        'nexo-sso.silent' => true,
        'nexo-sso.issuer' => 'https://nexoid.test',
        'nexo-sso.client_id' => '11111111-2222-3333-4444-555555555555',
    ]);

    // Throwaway guest pages carrying the middleware, so these tests don't
    // depend on any concrete route of the consuming tool. Multi-segment paths:
    // a tool's single-segment slug catch-all (/{username}) must never swallow them.
    Route::middleware(['web', NexoSsoSilentLogin::class])->get('/nexo-sso-tests/probe', fn () => response('silent-probe-ok'));
    Route::middleware(['web', NexoSsoSilentLogin::class])->post('/nexo-sso-tests/probe', fn () => response('silent-probe-ok'));
});

/** A request that presents this app's session cookie (any value: presence is what counts). */
function nexoSsoWithSessionCookie(TestCase $test): TestCase
{
    return $test->withCookie((string) config('session.cookie'), 'previously-issued');
}

test('AC-SILENT-1: a guest page hands an eligible visitor to the silent attempt, marking it once-per-session', function (): void {
    $response = nexoSsoWithSessionCookie($this)->get('/nexo-sso-tests/probe');

    $response->assertRedirect(route('nexo-sso.silent'));
    $response->assertSessionHas('nexo_sso.silent_attempted', true);
    // The visitor returns exactly where they were going.
    expect(session('url.intended'))->toBe(url('/nexo-sso-tests/probe'));
});

test('AC-SILENT-1: the silent authorize request is the standard one (PKCE+state+nonce) plus prompt=none', function (): void {
    nexoSsoFakeProvider();

    $response = $this->get(route('nexo-sso.silent'));

    $response->assertRedirect();
    parse_str((string) parse_url((string) $response->headers->get('Location'), PHP_URL_QUERY), $query);

    expect($query['prompt'])->toBe('none')
        ->and($query['response_type'])->toBe('code')
        ->and($query['redirect_uri'])->toBe(route('nexo-sso.callback'))
        ->and($query['state'])->toBe(session('nexo_sso.state'))
        ->and($query['nonce'])->toBe(session('nexo_sso.nonce'))
        ->and($query['code_challenge_method'])->toBe('S256')
        ->and($query['code_challenge'])->toBe(
            rtrim(strtr(base64_encode(hash('sha256', session('nexo_sso.verifier'), true)), '+/', '-_'), '=')
        );

    // The attempt is marked as silent, so the callback picks the silent UX.
    $response->assertSessionHas('nexo_sso.silent', true);
    $response->assertSessionHas('nexo_sso.silent_attempted', true);
});

test('AC-SILENT-1: a silent callback carrying a code signs the user in and returns to the destination', function (): void {
    nexoSsoFakeProvider(); // default id_token echoes the session nonce

    $response = $this
        ->withSession([
            'nexo_sso.state' => str_repeat('s', 40),
            'nexo_sso.verifier' => str_repeat('v', 64),
            'nexo_sso.nonce' => nexoSsoNonce(),
            'nexo_sso.silent' => true,
            'nexo_sso.silent_attempted' => true,
            'url.intended' => url('/target-page'),
        ])
        ->get(route('nexo-sso.callback', ['code' => 'auth-code', 'state' => str_repeat('s', 40)]));

    $this->assertAuthenticated();
    $response->assertRedirect(url('/target-page'));
});

test('AC-SILENT-2: login_required after a silent attempt stays guest, shows no error and returns to the destination', function (): void {
    Http::fake();

    $response = $this
        ->withSession([
            'nexo_sso.state' => str_repeat('s', 40),
            'nexo_sso.verifier' => str_repeat('v', 64),
            'nexo_sso.nonce' => nexoSsoNonce(),
            'nexo_sso.silent' => true,
            'nexo_sso.silent_attempted' => true,
            'url.intended' => url('/target-page'),
        ])
        ->get(route('nexo-sso.callback', ['error' => 'login_required', 'state' => str_repeat('s', 40)]));

    $this->assertGuest();
    $response->assertRedirect(url('/target-page'));
    $response->assertSessionHasNoErrors();
    // The once-per-session mark survives — no retry this session.
    $response->assertSessionHas('nexo_sso.silent_attempted', true);
    Http::assertNothingSent(); // an error response never triggers provider calls
});

test('AC-SILENT-2: interaction_required is handled the same silent way', function (): void {
    Http::fake();

    $response = $this
        ->withSession(['nexo_sso.silent' => true, 'nexo_sso.silent_attempted' => true, 'url.intended' => url('/target-page')])
        ->get(route('nexo-sso.callback', ['error' => 'interaction_required']));

    $this->assertGuest();
    $response->assertRedirect(url('/target-page'));
    $response->assertSessionHasNoErrors();
    Http::assertNothingSent();
});

test('AC-SILENT-2: the next pageload after a failed attempt does not redirect again (no loop)', function (): void {
    nexoSsoWithSessionCookie($this)
        ->withSession(['nexo_sso.silent_attempted' => true])
        ->get('/nexo-sso-tests/probe')
        ->assertOk()
        ->assertSee('silent-probe-ok');
});

test('AC-SILENT-2: an interactive (non-silent) error callback still surfaces the error', function (): void {
    $this
        ->withSession(['nexo_sso.state' => str_repeat('s', 40)])
        ->get(route('nexo-sso.callback', ['error' => 'access_denied', 'state' => str_repeat('s', 40)]))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('nexo_sso');
    $this->assertGuest();
});

test('AC-SILENT-3: with SSO disabled the middleware is a pass-through', function (): void {
    config(['nexo-sso.enabled' => false]);

    nexoSsoWithSessionCookie($this)->get('/nexo-sso-tests/probe')
        ->assertOk()
        ->assertSessionMissing('nexo_sso.silent_attempted');
});

test('AC-SILENT-3: the NEXO_SSO_SILENT kill-switch alone disables the attempt (and its route)', function (): void {
    config(['nexo-sso.silent' => false]);

    nexoSsoWithSessionCookie($this)->get('/nexo-sso-tests/probe')
        ->assertOk()
        ->assertSessionMissing('nexo_sso.silent_attempted');

    $this->get(route('nexo-sso.silent'))->assertNotFound();
});

test('AC-SILENT-4: a request without this app\'s session cookie never bounces (bots, monitors, first hit)', function (): void {
    $this->get('/nexo-sso-tests/probe')
        ->assertOk()
        ->assertSee('silent-probe-ok');
});

test('AC-SILENT-4: only guest GET page navigations are eligible', function (): void {
    // Authenticated: nothing to do.
    nexoSsoWithSessionCookie($this->actingAs(User::factory()->create()))
        ->get('/nexo-sso-tests/probe')->assertOk();

    // Non-GET: never redirected.
    nexoSsoWithSessionCookie($this)->post('/nexo-sso-tests/probe')->assertOk();

    // JSON/XHR: redirecting an API-ish request would break it.
    nexoSsoWithSessionCookie($this)->getJson('/nexo-sso-tests/probe')->assertOk();
});

test('AC-SILENT-4: excluded paths never attempt (public storefronts, machine endpoints)', function (): void {
    config(['nexo-sso.silent_excluded' => ['nexo-sso-tests/*']]);

    nexoSsoWithSessionCookie($this)->get('/nexo-sso-tests/probe')
        ->assertOk()
        ->assertSessionMissing('nexo_sso.silent_attempted');
});

test('AC-SILENT-4: excluded route NAMES never attempt (slug catch-alls)', function (): void {
    Route::middleware(['web', NexoSsoSilentLogin::class])
        ->get('/nexo-sso-tests/probe-named', fn () => response('named-ok'))
        ->name('silent.probe.named');
    config(['nexo-sso.silent_excluded_routes' => ['silent.probe.*']]);

    nexoSsoWithSessionCookie($this)->get('/nexo-sso-tests/probe-named')
        ->assertOk()
        ->assertSessionMissing('nexo_sso.silent_attempted');
});

test('AC-SILENT-5: logout marks the fresh session so silent SSO cannot immediately sign the user back in', function (): void {
    nexoSsoFakeProvider();

    $response = $this->actingAs(User::factory()->create())->post(route('nexo-sso.logout'));

    $response->assertRedirect(); // RP-initiated logout redirect (end_session)
    $this->assertGuest();
    // The fresh (post-invalidate) session already carries the mark: the next
    // guest pageload serves plainly instead of silently re-authenticating.
    $response->assertSessionHas('nexo_sso.silent_attempted', true);
});
