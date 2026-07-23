<?php

// Negative-and-positive coverage for the security-critical id_token validation
// branches in NexoSsoService::validateIdToken. These lock in behaviour that is
// correct today only because firebase/php-jwt binds the header `alg` to the
// key's algorithm — nothing else pins it. Without these tests a refactor (or a
// switch to a laxer JWT lib) could silently open algorithm-confusion or accept
// a kid-less token via the wrong branch, and it would fan out to every tool
// that copies this template.

use Firebase\JWT\JWT;
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

test('AC-FLOW-3: a kid-less single-key id_token validates (the real Nexo ID production path)', function (): void {
    // Nexo ID's OIDC bridge signs id_tokens WITHOUT a `kid` header. With a
    // single-key JWKS that is unambiguous, so validateIdToken takes the
    // JWK::parseKey branch. The rest of the suite signs with a kid (parseKeySet
    // branch), so this is the only test exercising the branch real logins use.
    nexoSsoFakeProvider([
        'access_token' => 'fake', 'token_type' => 'Bearer',
        'id_token' => nexoSsoIdToken([], null, null), // kid=null → no kid header
    ]);

    nexoSsoCallback($this)->assertRedirect(); // any redirect (consumer-specific landing)
    $this->assertAuthenticated();
});

test('AC-FLOW-3: an alg=none id_token is rejected (algorithm stripping)', function (): void {
    nexoSsoFakeProvider([
        'access_token' => 'fake', 'token_type' => 'Bearer',
        'id_token' => nexoSsoUnsignedToken(),
    ]);

    nexoSsoCallback($this)->assertRedirect(route('login'))->assertSessionHasErrors('nexo_sso');
    $this->assertGuest();
});

test('AC-FLOW-3: an alg=HS256 id_token signed with the RSA public key is rejected (RS/HS confusion)', function (): void {
    // The classic RS256→HS256 confusion: the attacker signs an HS256 token using
    // the provider's PUBLIC key (public knowledge) as the HMAC secret, hoping the
    // verifier treats the public key as a shared secret. The RS256-pinned key
    // must refuse it.
    $forged = JWT::encode(nexoSsoClaims(), nexoSsoKeypair()['public'], 'HS256', null);

    nexoSsoFakeProvider([
        'access_token' => 'fake', 'token_type' => 'Bearer',
        'id_token' => $forged,
    ]);

    nexoSsoCallback($this)->assertRedirect(route('login'))->assertSessionHasErrors('nexo_sso');
    $this->assertGuest();
});
