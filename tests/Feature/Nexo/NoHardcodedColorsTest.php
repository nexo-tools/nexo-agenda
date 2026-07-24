<?php

// Guardian: brand colors come from nexo-brand tokens (var(--nexo-*)), never raw
// hex in Blade views or app CSS. Adjust $allowed for files that legitimately hold
// literal colors (the generated tokens file, per-business/storefront theming, and
// <meta>/<input> values that can't reference a CSS var). SVGs under public/ are
// not scanned.

use RecursiveDirectoryIterator as Dir;
use RecursiveIteratorIterator as Walk;

it('has no hardcoded hex colors in blade views or app css (use --nexo-* tokens)', function () {
    $roots = array_filter([resource_path('views'), resource_path('css')], 'is_dir');

    // Relative paths (from resource_path) allowed to contain literal hex:
    //  - the generated brand tokens (the one place raw palette hex lives);
    //  - public-layout: the per-business accent + the teal storefront scale, a
    //    product feature that is deliberately decoupled from the Nexo brand;
    //  - settings/edit: the <input type="color"> default value must be a literal;
    //  - head/welcome: the <meta name="theme-color"> content can't be a CSS var.
    $allowed = [
        'css/nexo-tokens.css',
        'views/components/public-layout.blade.php',
        'views/app/settings/edit.blade.php',
        'views/partials/head.blade.php',
        'views/welcome.blade.php',
    ];

    $base = resource_path().DIRECTORY_SEPARATOR;
    $offenders = [];

    foreach ($roots as $root) {
        foreach (new Walk(new Dir($root, FilesystemIterator::SKIP_DOTS)) as $file) {
            if (! preg_match('/\.(blade\.php|css)$/', $file->getFilename())) {
                continue;
            }

            $rel = str_replace([$base, DIRECTORY_SEPARATOR], ['', '/'], $file->getPathname());

            // Transactional emails must use literal inline colors: mail clients do
            // not support CSS custom properties, so tokens can't be used there.
            if (str_starts_with($rel, 'views/emails/')) {
                continue;
            }

            if (in_array($rel, $allowed, true)) {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            if (preg_match_all('/#[0-9a-fA-F]{3,8}\b/', $contents, $m)) {
                $offenders[] = $rel.' -> '.implode(', ', array_unique($m[0]));
            }
        }
    }

    expect($offenders)->toBe([], "Hardcoded hex colors found (use var(--nexo-*)):\n".implode("\n", $offenders));
});
