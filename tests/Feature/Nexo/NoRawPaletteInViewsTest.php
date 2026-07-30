<?php

// Guardian: views consume the semantic utility layer (bg-surface, text-ink,
// text-muted, border-line), never raw Tailwind palette classes. Copy into
// tests/Feature/Nexo/.
//
// Why this exists: NoHardcodedColorsTest only catches literal hex. The drift
// that actually broke dark mode across the ecosystem was `bg-white
// dark:bg-slate-800` and `text-gray-600` written by hand in Blade — every view
// re-deciding its own dark pair, and each one a chance to forget. Views that
// hand-roll a `dark:` pair are views that will ship a light-only screen.
//
// $allowed is for views that legitimately sit outside the token system (a
// cookieless standalone host with its own inline shell, a per-page themed public
// page). Each entry needs a reason — an allow-list without one is a silence.

use RecursiveDirectoryIterator as Dir;
use RecursiveIteratorIterator as Walk;

it('has no raw Tailwind palette classes in views (use the semantic utilities)', function () {
    $root = resource_path('views');

    // Views exempt from the semantic layer, each with its reason.
    $allowed = [
        // 'components/layout.blade.php' => 'ADR-001: cookieless short host, self-contained shell',
    ];

    // slate/gray/zinc/neutral/stone as text|bg|border|ring|divide, plus the
    // scaffold accents (indigo/purple) that are not the brand violet.
    $pattern = '/\b(?:dark:)?(?:text|bg|border|ring|divide|placeholder)-'
        .'(?:slate|gray|zinc|neutral|stone|indigo)-(?:50|\d{3})\b/';

    $offenders = [];
    foreach (new Walk(new Dir($root, FilesystemIterator::SKIP_DOTS)) as $file) {
        if (! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }

        $relative = str_replace($root.DIRECTORY_SEPARATOR, '', $file->getPathname());
        if (array_key_exists($relative, $allowed)) {
            continue;
        }

        $contents = file_get_contents($file->getPathname());
        if (preg_match_all($pattern, $contents, $m)) {
            $hits = array_unique($m[0]);
            $offenders[] = $relative.' -> '.implode(', ', array_slice($hits, 0, 6))
                .(count($hits) > 6 ? ' (+'.(count($hits) - 6).' more)' : '');
        }
    }

    expect($offenders)->toBe([], "Raw palette classes found. Use the semantic utilities\n"
        ."(bg-surface / bg-bg / text-ink / text-muted / border-line / ring-ring)\n"
        ."so light and dark flip from one token instead of a hand-written pair:\n"
        .implode("\n", $offenders));
});
