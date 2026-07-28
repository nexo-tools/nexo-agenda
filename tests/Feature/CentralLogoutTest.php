<?php

/**
 * Central logout wiring. When SSO is enabled the sign-out button must end the
 * Nexo ID session too, not just this tool's - otherwise "log out" is a lie
 * across the ecosystem: the next tool you open silently signs you straight back
 * in from the still-live provider session.
 *
 * Guarded because it is a per-view detail that drifts silently: a new layout
 * copied from an old one reintroduces the local-only form and nothing fails.
 */
it('AC-LOGOUT-2: every logout form goes through the central logout when SSO is on', function (): void {
    $offenders = [];

    foreach (rglob(resource_path('views'), '*.blade.php') as $file) {
        $contents = (string) file_get_contents($file);
        if (str_contains($contents, 'action="{{ route(\'logout\') }}"')) {
            $offenders[] = str_replace(resource_path('views').'/', '', $file);
        }
    }

    expect($offenders)->toBe([], 'These views log out locally only: '.implode(', ', $offenders));
});

/** @return array<int, string> */
function rglob(string $dir, string $pattern): array
{
    $found = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (fnmatch($pattern, $file->getFilename())) {
            $found[] = $file->getPathname();
        }
    }

    return $found;
}
