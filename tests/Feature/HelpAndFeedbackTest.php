<?php

use App\Models\FeedbackReport;

it('shows the help center with FAQ', function () {
    $this->get('/help')
        ->assertOk()
        ->assertSee('Centro de ayuda')
        ->assertSee('¿Necesito una cuenta para reservar?');
});

it('translates the help center', function () {
    $this->get('/help?lang=en')
        ->assertOk()
        ->assertSee('Help center')
        ->assertSee('Do I need an account to book?');
});

it('redirects the old Spanish help path for good', function () {
    // /ayuda lived in sitemaps, in inboxes and in browser histories before the
    // ecosystem settled on one URL for the six tools (decision U1, 2026-08-02).
    $this->get('/ayuda')->assertRedirect('/help')->assertStatus(301);
});

it('shows the contact form', function () {
    $this->get('/contacto')
        ->assertOk()
        ->assertSee('Escríbenos')
        ->assertSee('Reportar un problema');
});

it('stores a feedback report', function () {
    $response = $this->post('/contacto', [
        'type' => 'bug',
        'message' => 'El botón de reservar no responde en mi teléfono.',
        'email' => 'cliente@example.com',
    ]);

    $response->assertRedirect('/contacto');
    $response->assertSessionHas('status');

    expect(FeedbackReport::count())->toBe(1);
    expect(FeedbackReport::first())
        ->type->toBe('bug')
        ->email->toBe('cliente@example.com');
});

it('rejects an invalid feedback type', function () {
    $this->post('/contacto', [
        'type' => 'spam',
        'message' => 'x',
    ])->assertSessionHasErrors('type');

    expect(FeedbackReport::count())->toBe(0);
});

it('requires a message', function () {
    $this->post('/contacto', ['type' => 'idea'])
        ->assertSessionHasErrors('message');
});
