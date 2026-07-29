<?php

use Illuminate\Support\Facades\Route;

// Guardian: the static surfaces every Nexo tool must have — error pages with the
// tool's identity, and legal pages — exist, answer, and are translated.
//
// Why it exists: 404/419/429/500 and privacy/terms are the pages nobody opens
// while building, so they rot silently. 419 and 429 in particular are the ones a
// real user hits (expired session, rate limit) and the ones most often left as
// Laravel's untranslated default.
//
// Copy into tests/Feature/Nexo/. If a tool legitimately lacks a page (an isolated
// short-link host only needs a minimal 404), drop it from $codes and record the
// exception in the tool's AGENTS.md.
//
// Pest note: toContain() is variadic — a second argument is another needle, not
// a failure message — so human-readable messages go through toBeTrue()/toBeFalse().

$codes = [403, 404, 419, 429, 500, 503];

it('ships an error view for every code the standard requires', function () use ($codes) {
    $missing = array_values(array_filter(
        $codes,
        fn (int $code) => ! is_file(resource_path("views/errors/{$code}.blade.php"))
    ));

    expect($missing)->toBe([], 'Missing error views (copy from templates/nexo-ui/pages/errors/): '.implode(', ', $missing));
});

it('renders error pages with the tool chrome and no untranslated placeholders', function () use ($codes) {
    foreach ($codes as $code) {
        $path = resource_path("views/errors/{$code}.blade.php");
        if (! is_file($path)) {
            continue;
        }

        $contents = (string) file_get_contents($path);

        // Strings go through __() so the generator can translate them.
        expect(str_contains($contents, '__('))->toBeTrue("errors/{$code}.blade.php has hardcoded strings — wrap them in __().");
        expect(str_contains($contents, '[COMPLETAR'))->toBeFalse("errors/{$code}.blade.php still has a template placeholder.");
    }
});

it('serves a branded 404 instead of the framework default', function () {
    // Local convention: the branded error views only render with debug off
    // (otherwise Laravel serves Ignition), so the guardian asks for the
    // production behaviour explicitly. Same as tests/Feature/ErrorPagesTest.
    config(['app.debug' => false]);

    $html = $this->get('/this-path-does-not-exist-'.uniqid())
        ->assertNotFound()
        ->getContent();

    // The chrome renders, so the page belongs to the product.
    expect($html)->toContain('404');
    expect(str_contains($html, 'Whoops, looks like something went wrong'))->toBeFalse('Laravel default error page is still being served.');
});

it('serves the legal pages and links them from each other', function () {
    foreach (['legal.privacy', 'legal.terms'] as $route) {
        expect(Route::has($route))->toBeTrue("Route {$route} is not registered (see templates/nexo-ui/pages/legal/routes-snippet.php).");

        $html = $this->get(route($route))->assertOk()->getContent();

        expect(str_contains($html, '[COMPLETAR'))->toBeFalse("The {$route} page still ships a template placeholder — write this tool's real content before shipping.");
        expect($html)->toContain(route('legal.privacy'));
        expect($html)->toContain(route('legal.terms'));
    }
});

it('translates the legal content in every supported locale', function () {
    foreach (['es', 'en', 'pt'] as $locale) {
        $path = lang_path("{$locale}/legal.php");
        expect(is_file($path))->toBeTrue("lang/{$locale}/legal.php is missing.");

        $content = require $path;

        foreach (['privacy', 'terms'] as $page) {
            expect($content[$page]['title'] ?? null)->not->toBeNull("legal.{$page}.title missing in {$locale}.");
            expect($content[$page]['sections'] ?? [])->not->toBeEmpty("legal.{$page}.sections empty in {$locale}.");
        }

        expect(str_contains((string) json_encode($content), '[COMPLETAR'))->toBeFalse("lang/{$locale}/legal.php still has template placeholders.");
    }
});

it('names the instance operator on the legal pages when configured', function () {
    // The env-driven operator/contact contract (templates/nexo-ui/pages): with
    // the values set, the pages must say who answers for this instance. One
    // tool shipped legal pages that never consumed these values while the
    // deploy pipeline delivered them — a no-op that read as done. Asserting the
    // rendered output is what catches that class of gap.
    config()->set('nexo.legal.operator', 'Example Operator');
    config()->set('nexo.legal.contact', 'legal@example.test');

    $html = $this->get(route('legal.privacy'))->assertOk()->getContent();

    expect(str_contains($html, 'Example Operator'))->toBeTrue('The operator section did not render.');
    expect(str_contains($html, 'legal@example.test'))->toBeTrue('The contact did not render.');
});
