<?php

use App\Models\Business;

it('exposes a skip link and language on public pages', function () {
    $business = Business::factory()->create();

    $response = $this->get('/'.$business->slug);

    $response->assertOk();
    $response->assertSee('href="#contenido"', false);
    $response->assertSee('<html lang="es"', false);
});

it('links validation errors to their field for assistive tech', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
    ]);

    $response->assertRedirect('/register');

    $follow = $this->get('/register');
    $follow->assertSee('aria-invalid="true"', false);
    $follow->assertSee('aria-describedby="email-error"', false);
    $follow->assertSee('id="email-error"', false);
});
