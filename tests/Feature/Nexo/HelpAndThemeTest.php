<?php

// Guardian: /help is the SAME help center in every tool — canonical URL, a real
// translated title, actual questions, a way to reach a human, and a link to it
// from the shared chrome — and the theme-init + tokens are wired into the shell
// (so light/dark works everywhere). Copy into tests/Feature/Nexo/.
//
// Why v2 (2026-08-02): v1 asserted 200 plus assertSee(__('nexo.help.title')) and
// nothing else, so it was structurally blind to everything the audit found — a
// tool serving the page at /ayuda, a controller passing no data at all, an
// intro key left orphaned, contact blocks pointing to three different things,
// FAQ counts from 4 to 8, and no link to any of it in half the chromes. Worse,
// assertSee(__('key')) is self-referential: with the translation missing the
// view prints the raw key and the test compares against that same raw key, so
// an untranslated page passed.
//
// The ONE adaptation allowed when copying: the URL builders below, for a tool
// whose panel lives on its own host (nexoshort overrides them with panelUrl()).
// The PATH is /help in all six — nexoagenda's old /ayuda now 301s to it
// (decision U1, 2026-08-02).
//
// Pest note: toContain() is variadic — a second argument is another needle, not
// a failure message — so human-readable messages go through toBeTrue()/toBe().

const HELP_URL = '/help';

/** nexoshort overrides this body with panelUrl(HELP_URL.$query): its panel is another host. */
function helpUrl(string $query = ''): string
{
    return HELP_URL.$query;
}

/** The public shell whose chrome must link the help center. nexoshort: panelUrl('/'). */
function shellUrl(): string
{
    return '/';
}

it('serves the help center at the canonical URL', function () {
    $this->get(helpUrl())->assertOk();
});

it('renders a title that is actually translated', function () {
    // If the key is missing, __() returns the key itself — the failure v1 could
    // not see, because it compared the page against that same string.
    expect(__('nexo.help.title'))->not->toBe('nexo.help.title', 'nexo.help.title is not translated — the page would print the raw key.');

    $this->get(helpUrl())->assertOk()->assertSee(__('nexo.help.title'));
});

it('answers real questions and offers a way to reach a human', function () {
    $html = $this->get(helpUrl())->assertOk()->getContent();

    // A help center with zero questions passed v1 happily.
    expect(preg_match_all('/<details[\s>]/i', $html))
        ->toBeGreaterThan(0, 'The help center renders no <details> — its FAQs come from lang/{locale}/help.php.');

    // Every tool answers contact differently (mailto, config, an internal
    // form); what the standard fixes is that the block is there.
    expect(str_contains($html, 'nexo-help__contact'))->toBeTrue('The help center has no contact block (.nexo-help__contact).');
});

it('serves the help center in the three locales', function () {
    foreach (['es', 'en', 'pt'] as $locale) {
        $html = $this->get(helpUrl('?lang='.$locale))->assertOk()->getContent();

        $title = trans('nexo.help.title', [], $locale);
        expect($title)->not->toBe('nexo.help.title', "nexo.help.title missing in lang/{$locale}.");
        expect(str_contains($html, $title))->toBeTrue("The help center did not render in {$locale} (expected the title \"{$title}\").");
    }
});

it('links the help center from the shared footer', function () {
    $html = $this->get(shellUrl())->assertOk()->getContent();

    // Specifically the FOOTER, not "somewhere on the page": a link in a landing
    // header is exactly what several tools already had, and it is why the help
    // center was unreachable from everywhere else. The footer renders on every
    // surface, signed in or not.
    $start = strpos($html, '<footer');
    expect($start)->not->toBeFalse('The page renders no <footer> — copy the canonical nexo-footer.');

    expect(str_contains(substr($html, $start), route('help')))
        ->toBeTrue('The shared footer does not link route(\'help\') — copy the canonical nexo-footer.');
});

it('links the help center from the shared header too', function () {
    $html = $this->get(shellUrl())->assertOk()->getContent();

    // Since 2026-08-02 the canonical nexo-header bakes the help link in as a
    // plain text nav link: before, each tool placed it wherever (ghost button,
    // nav link, or nowhere) and the drift was visible when crossing tools. The
    // footer copy above stays — the header nav is hidden on mobile.
    $start = strpos($html, '<header');
    expect($start)->not->toBeFalse('The page renders no <header> — copy the canonical nexo-header.');

    $end = strpos($html, '</header>', $start);
    expect(str_contains(substr($html, $start, $end === false ? null : $end - $start), route('help')))
        ->toBeTrue('The shared header does not link route(\'help\') — copy the canonical nexo-header (the help link is baked in).');
});

it('stamps the theme before paint and ships the tokens (light/dark ready)', function () {
    $html = $this->get(shellUrl())->assertOk()->getContent();

    // The FOUC-free theme init sets <html data-theme> ...
    expect($html)->toContain('data-theme');
    // ... and the brand layer is wired into the shell: either the token stylesheet
    // (inline --nexo-*, or the compiled Vite link) or the token-styled chrome
    // (nexo-header/nexo-footer), so it holds with or without a built frontend.
    expect($html)->toMatch('#--nexo-|nexo-brand|tokens\.css|app\.css|/build/assets/app-|nexo-header|nexo-footer#');
});
