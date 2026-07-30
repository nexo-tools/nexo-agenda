<?php

use App\Models\Business;
use App\Models\Service;
use Illuminate\Support\Facades\Process;

beforeEach(function () {
    $this->business = Business::factory()->create(['slug' => 'idiomas-test']);
    Service::factory()->for($this->business)->create();
});

it('defaults to spanish', function () {
    $this->get('/idiomas-test')->assertSee('Reserva tu turno');
});

it('falls back to the english source string when a key has no translation', function () {
    // English is the key language: lang/en.json does not exist on purpose.
    expect(file_exists(lang_path('en.json')))->toBeFalse();

    $this->get('/idiomas-test?lang=en')->assertSee('Book your appointment');
});

it('honours the accept-language header', function () {
    $this->get('/idiomas-test', ['Accept-Language' => 'pt-BR,pt;q=0.9'])
        ->assertSee('Agende seu horário');

    $this->get('/idiomas-test', ['Accept-Language' => 'en-US,en;q=0.9'])
        ->assertSee('Book your appointment');
});

it('switches with the lang parameter and persists via the shared nexo-lang cookie', function () {
    $this->get('/idiomas-test?lang=en')
        ->assertSee('Book your appointment')
        ->assertPlainCookie('nexo-lang', 'en');

    // A later request carrying the cookie keeps English (shared across tools).
    $this->withUnencryptedCookie('nexo-lang', 'en')
        ->get('/idiomas-test')->assertSee('Book your appointment');
});

it('ignores unsupported locales', function () {
    $this->get('/idiomas-test?lang=fr')->assertSee('Reserva tu turno');
});

it('shows the locale switcher', function () {
    $this->get('/idiomas-test')
        ->assertSee('lang=es', false)
        ->assertSee('lang=en', false)
        ->assertSee('lang=pt', false);
});

it('translates validation messages', function () {
    $response = $this->post('/register', [], ['Accept-Language' => 'pt']);

    $response->assertSessionHasErrors();
    expect(session('errors')->first('name'))->toContain('obrigatório');
});

it('keeps every string translated in es and pt', function () {
    $result = Process::path(base_path())
        ->run('node scripts/generate-translations.mjs --check');

    expect($result->exitCode())->toBe(0, 'Missing translations: '.$result->errorOutput());
})->skip(fn () => Process::run('which node')->failed(), 'node not available');
