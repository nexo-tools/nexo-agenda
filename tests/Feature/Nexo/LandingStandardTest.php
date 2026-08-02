<?php

// Guardian: the public landing follows the family design system (design.md +
// STANDARD.md "Landing pública") — canonical 5-section skeleton in order, a
// single h1, the primary CTA in hero and cierre, the shared chrome, and none
// of the AI fingerprints the system bans (gradients, 100vh heroes,
// transition: all, emoji icons, the es copy tells).
//
// Copy into tests/Feature/Nexo/. Every tool names its landing view and CSS
// differently — adjust the two constants below when copying (e.g. the hub
// renders views/home.blade.php, nexoshort views/welcome.blade.php).
//
// The guardian is copied BEFORE the landing migrates to the skeleton, so the
// skeleton-dependent checks skip themselves (with a clear message) until the
// rendered page carries at least one data-landing-section marker — a red
// suite on day one would teach everyone to ignore this file.
//
// Pest note: toContain() is variadic — a second argument is another needle, not
// a failure message — so human-readable messages go through toBeTrue()/toBe().

use RecursiveDirectoryIterator as Dir;
use RecursiveIteratorIterator as Walk;

// Relative to resource_path(). Each tool adjusts these when copying.
const LANDING_VIEW = 'views/welcome.blade.php';
const LANDING_CSS = 'css/landing.css';

// The canonical skeleton, in order (design.md "Section skeleton").
const LANDING_SECTIONS = ['hero', 'producto', 'datos', 'preguntas', 'cierre'];

it('serves the landing with exactly one h1', function () {
    $html = $this->get('/')->assertOk()->getContent();

    expect(preg_match_all('/<h1[\s>]/i', $html))
        ->toBe(1, 'The landing must have exactly one <h1> — the tool\'s claim from the registry.');
});

it('wears the shared chrome (nexo-header and nexo-footer)', function () {
    $html = $this->get('/')->assertOk()->getContent();

    // The shared chrome IS the family's nav/footer (design.md "Family") —
    // a landing that invents its own marketing header has left the system.
    expect(str_contains($html, 'nexo-header'))->toBeTrue('The landing does not render x-nexo-header.');
    expect(str_contains($html, 'nexo-footer'))->toBeTrue('The landing does not render x-nexo-footer.');
});

it('renders the five canonical sections in order', function () {
    $html = $this->get('/')->assertOk()->getContent();

    if (! str_contains($html, 'data-landing-section')) {
        $this->markTestSkipped('Landing not yet migrated to the canonical skeleton (no data-landing-section in the rendered page) — see STANDARD.md "Landing pública".');
    }

    preg_match_all('/data-landing-section="([^"]+)"/', $html, $m);

    expect($m[1])->toBe(
        LANDING_SECTIONS,
        'The landing sections must be exactly hero → producto → datos → preguntas → cierre, each tagged once. Found: '.implode(' → ', $m[1])
    );
});

it('places the primary CTA in hero and again in cierre', function () {
    $html = $this->get('/')->assertOk()->getContent();

    if (! str_contains($html, 'data-landing-section')) {
        $this->markTestSkipped('Landing not yet migrated to the canonical skeleton (no data-landing-section in the rendered page) — see STANDARD.md "Landing pública".');
    }

    $marker = fn (string $name): string => 'data-landing-section="'.$name.'"';
    foreach (['hero', 'producto', 'cierre'] as $name) {
        expect(str_contains($html, $marker($name)))->toBeTrue("Section \"{$name}\" is missing — the CTA check needs it to slice the page.");
    }

    // One primary CTA in hero, the same verb again in cierre (design.md "CTA voice").
    $hero = substr($html, strpos($html, $marker('hero')), strpos($html, $marker('producto')) - strpos($html, $marker('hero')));
    $cierre = substr($html, strpos($html, $marker('cierre')));

    expect(str_contains($hero, 'nexo-btn--primary'))->toBeTrue('The hero has no primary CTA (.nexo-btn--primary).');
    expect(str_contains($cierre, 'nexo-btn--primary'))->toBeTrue('The cierre has no primary CTA (.nexo-btn--primary).');
});

it('keeps the AI fingerprints out of the landing view and CSS', function () {
    $files = array_filter([resource_path(LANDING_VIEW), resource_path(LANDING_CSS)], 'is_file');

    if ($files === []) {
        $this->markTestSkipped('Neither '.LANDING_VIEW.' nor '.LANDING_CSS.' exists — adjust the LANDING_VIEW/LANDING_CSS constants to this tool\'s file names.');
    }

    $view = resource_path(LANDING_VIEW);
    if (! is_file($view) || ! str_contains((string) file_get_contents($view), 'data-landing-section')) {
        $this->markTestSkipped('Landing not yet migrated to the canonical skeleton (no data-landing-section in '.LANDING_VIEW.') — see STANDARD.md "Landing pública".');
    }

    // The hard greps of STANDARD.md "Anti-fingerprint IA". Tailwind spellings
    // of the same sins are included — a class is just CSS with a shorter name.
    $fingerprints = [
        'gradient (banned: violet is solid-only)' => '/linear-gradient|radial-gradient|background-clip:\s*text/i',
        'full-viewport hero (100vh)' => '/min-height:\s*100(d|s|l)?vh|\bmin-h-(screen|dvh|svh|lvh)\b/i',
        'transition: all' => '/transition(-property)?:\s*all|\btransition-all\b/i',
        'emoji as icon' => '/[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}]/u',
    ];

    $offenders = [];
    foreach ($files as $path) {
        $contents = (string) file_get_contents($path);
        foreach ($fingerprints as $label => $pattern) {
            if (preg_match($pattern, $contents)) {
                $offenders[] = $path.' -> '.$label;
            }
        }
    }

    expect($offenders)->toBe([], "AI fingerprints found in the landing (see design.md):\n".implode("\n", $offenders));
});

it('opens no copy with the banned es tells', function () {
    $view = resource_path(LANDING_VIEW);
    if (! is_file($view) || ! str_contains((string) file_get_contents($view), 'data-landing-section')) {
        $this->markTestSkipped('Landing not yet migrated to the canonical skeleton (no data-landing-section in '.LANDING_VIEW.') — see STANDARD.md "Landing pública".');
    }

    // The banned openings of design.md "Copy" — the phrases that read as
    // machine-written Spanish. Scanned over the landing view AND lang/, because
    // the copy that renders usually lives in the translation maps.
    $banned = [
        '/potencia tu/iu',
        '/desata el poder/iu',
        '/desbloquea el poder/iu',
        '/se encuentra con/iu',
        '/empodera/iu',
        '/reimagina/iu',
        '/impulsa tu flujo/iu',
        '/soluciones innovadoras/iu',
        '/integración perfecta/iu',
        '/sin fisuras/iu',
        '/en el panorama digital/iu',
        '/al siguiente nivel/iu',
        '/de última generación/iu',
        '/empezar ahora/iu',
        '/click aquí/iu',
    ];

    $targets = [$view];
    if (is_dir(lang_path())) {
        /** @var SplFileInfo $file */
        foreach (new Walk(new Dir(lang_path(), FilesystemIterator::SKIP_DOTS)) as $file) {
            if (preg_match('/\.(php|json)$/', $file->getFilename())) {
                $targets[] = $file->getPathname();
            }
        }
    }

    $offenders = [];
    foreach ($targets as $path) {
        $contents = (string) file_get_contents($path);
        foreach ($banned as $pattern) {
            if (preg_match($pattern, $contents, $m)) {
                $offenders[] = $path.' -> "'.$m[0].'"';
            }
        }
    }

    expect($offenders)->toBe([], "Banned copy openings found (rewrite in a person's voice, see design.md \"Copy\"):\n".implode("\n", $offenders));
});

it('stamps the hallmark header as the first line of the landing CSS', function () {
    $view = resource_path(LANDING_VIEW);
    if (! is_file($view) || ! str_contains((string) file_get_contents($view), 'data-landing-section')) {
        $this->markTestSkipped('Landing not yet migrated to the canonical skeleton (no data-landing-section in '.LANDING_VIEW.') — see STANDARD.md "Landing pública".');
    }

    $css = resource_path(LANDING_CSS);
    expect(is_file($css))->toBeTrue(LANDING_CSS.' is missing — the landing CSS carries the hallmark stamp.');

    $first = '';
    foreach (preg_split('/\R/', (string) file_get_contents($css)) as $line) {
        if (trim($line) !== '') {
            $first = trim($line);
            break;
        }
    }

    // Future audits grade execution against the declared system, not against
    // rotation — the stamp is how they read it (design.md "Family").
    expect(preg_match('/^\/\* Hallmark ·/u', $first))
        ->toBe(1, 'The first non-empty line of '.LANDING_CSS.' must be the Hallmark stamp (/* Hallmark · macrostructure: Workbench · … */). Found: "'.$first.'"');
});
