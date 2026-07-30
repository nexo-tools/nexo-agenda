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

it('keeps button styling in the button component, focus ring included', function () {
    // Seven views had hand-copied the filled-button class string, and the copies
    // dropped `focus-visible:ring-*` — so keyboard users got the browser default
    // outline on exactly the primary actions (create booking, new service, book
    // now). Focus styling has to live in one place to stay true.
    $offenders = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('views'), FilesystemIterator::SKIP_DOTS)) as $file) {
        if (! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }

        // The component itself, and the chrome layer's own .nexo-btn classes.
        if ($file->getFilename() === 'button.blade.php') {
            continue;
        }

        $contents = (string) file_get_contents($file->getPathname());

        if (preg_match('/class="[^"]*\bbg-brand-700\b[^"]*"/', $contents)) {
            $offenders[] = $file->getPathname();
        }
    }

    expect($offenders)->toBe([], "Hand-rolled filled buttons found — use <x-button>:\n".implode("\n", $offenders));
});
