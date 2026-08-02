<?php

// Guardian: the schema follows DATABASE-STANDARD.md (alvaro/DATABASE-STANDARD.md).
//
// Why it exists: the 2026-08-02 inventory found the database layer to be the
// healthiest part of the ecosystem — and entirely unguarded. Every convention
// held because the same person wrote all six schemas in the same month, which
// is not a mechanism. The first migration written by somebody else (or by an
// agent) is where it stops holding, and a schema mistake is the expensive kind:
// it survives in production data long after the code that made it was fixed.
//
// Copy into tests/Feature/Nexo/ and fill the constants with this tool's real
// schema. Skip-until-filled: with EXPECTED_TABLES empty every test here skips
// with a message, so the file can travel ahead of the work.
//
// Pest note: toContain() is variadic — a second argument is another needle, not
// a failure message — so human-readable messages go through toBeTrue()/toBe().

use Illuminate\Support\Facades\Schema;

/**
 * Every table this tool's migrations create, framework ones included. It is a
 * declaration, not a mirror: the point is that a table appearing (or vanishing)
 * without anybody deciding it shows up as a failure.
 */
const EXPECTED_TABLES = [
    // Framework
    'cache', 'cache_locks', 'failed_jobs', 'job_batches', 'jobs',
    'password_reset_tokens', 'sessions', 'users',
    // Domain
    'absences', 'bookings', 'businesses', 'feedback_reports', 'page_visits',
    'professionals', 'reviews', 'schedule_blocks', 'services', 'waitlist_entries',
];

/**
 * Columns that hold a secret in the clear, as 'table.column'. The standard says
 * tokens are persisted hashed in a *_hash column; anything listed here is a
 * documented exception (DATABASE-STANDARD.md §12), not a licence.
 */
const TOKEN_EXCEPTIONS = [
    // Documented exception (DATABASE-STANDARD.md §5 and §12): hashing it would
    // rotate every calendar feed URL already subscribed in third-party clients,
    // and the feed is read-only. Revisit only with a planned rotation.
    'professionals.feed_token',

    // Not an exception to the RULE — this column holds the sha256, never the raw
    // token (Booking::newManagementToken) — only to the naming: it predates the
    // *_hash convention and renaming a column with live data buys no behaviour.
    'bookings.management_token',
];

/** Money columns, as 'table.column'. They must be DECIMAL, never float. */
const MONEY_COLUMNS = [
    // The only money column in the whole ecosystem. Currency is ARS, implicit,
    // documented in the Service model.
    'services.price',
];

/** Tables whose names the framework owns and that do not follow our plural rule. */
const FRAMEWORK_TABLES = [
    'migrations', 'cache', 'cache_locks', 'job_batches', 'password_reset_tokens', 'sessions',
];

it('creates exactly the tables this tool declares', function () {
    if (EXPECTED_TABLES === []) {
        test()->markTestSkipped('EXPECTED_TABLES is empty — declare this tool\'s schema (see alvaro/DATABASE-STANDARD.md).');
    }

    $actual = collect(Schema::getTableListing())
        ->map(fn (string $table): string => str_contains($table, '.') ? substr($table, strrpos($table, '.') + 1) : $table)
        ->reject(fn (string $table): bool => $table === 'migrations')
        ->sort()
        ->values()
        ->all();

    $expected = collect(EXPECTED_TABLES)->reject(fn (string $t): bool => $t === 'migrations')->sort()->values()->all();

    expect($actual)->toBe(
        $expected,
        "The schema no longer matches the declared table list.\nAdded: ".implode(', ', array_diff($actual, $expected))
        ."\nMissing: ".implode(', ', array_diff($expected, $actual))
    );
});

it('keeps timezone-aware columns out of the schema', function () {
    // UTC in the database, conversion only at presentation time with an explicit
    // domain timezone (DATABASE-STANDARD.md §3). A tz-aware column inside an app
    // running on UTC is how an hour goes missing in production without a trace.
    $offenders = [];

    foreach (glob(database_path('migrations/*.php')) as $file) {
        $contents = (string) file_get_contents($file);
        if (preg_match('/timestampTz|dateTimeTz|timeTz/', $contents)) {
            $offenders[] = basename($file);
        }
    }

    expect($offenders)->toBe([], "Timezone-aware columns found (use plain timestamp()/dateTime(), UTC in the DB):\n".implode("\n", $offenders));
});

it('stores tokens hashed, or names the exception out loud', function () {
    if (EXPECTED_TABLES === []) {
        test()->markTestSkipped('EXPECTED_TABLES is empty — declare this tool\'s schema first.');
    }

    $offenders = [];

    foreach (EXPECTED_TABLES as $table) {
        if (! Schema::hasTable($table)) {
            continue;
        }

        foreach (Schema::getColumnListing($table) as $column) {
            // A column that holds a secret says so in its name. Anything called
            // *token* or *secret* is either hashed (and named _hash) or an
            // exception somebody wrote down.
            if (! preg_match('/(token|secret)/i', $column)) {
                continue;
            }
            if (str_ends_with($column, '_hash') || in_array("{$table}.{$column}", TOKEN_EXCEPTIONS, true)) {
                continue;
            }
            // The framework's own plumbing (remember_token, password_reset_tokens.token)
            // is upstream's call, not this schema's.
            if (in_array($table, FRAMEWORK_TABLES, true) || $column === 'remember_token') {
                continue;
            }

            $offenders[] = "{$table}.{$column}";
        }
    }

    expect($offenders)->toBe(
        [],
        "Raw token columns (hash them into a *_hash column, or add them to TOKEN_EXCEPTIONS with a why in DATABASE-STANDARD.md §12):\n".implode("\n", $offenders)
    );
});

it('keeps money in decimal, never in a float', function () {
    if (MONEY_COLUMNS === []) {
        test()->markTestSkipped('This tool has no money columns.');
    }

    foreach (MONEY_COLUMNS as $path) {
        [$table, $column] = explode('.', $path);

        expect(Schema::hasColumn($table, $column))->toBeTrue("Declared money column {$path} does not exist.");

        $type = Schema::getColumnType($table, $column);

        // A float holding money is a rounding error waiting for a month-end.
        // sqlite (what the suite runs on) reports DECIMAL as "numeric" — its
        // type affinity, same storage class, so both spellings pass.
        expect(preg_match('/decimal|numeric/i', $type))
            ->toBe(1, "Money column {$path} is {$type}, not decimal (DATABASE-STANDARD.md §4).");
    }
});

it('names tables in snake_case english plural', function () {
    if (EXPECTED_TABLES === []) {
        test()->markTestSkipped('EXPECTED_TABLES is empty — declare this tool\'s schema first.');
    }

    $offenders = [];

    foreach (EXPECTED_TABLES as $table) {
        if (in_array($table, FRAMEWORK_TABLES, true)) {
            continue;
        }
        if (! preg_match('/^[a-z][a-z0-9_]*$/', $table)) {
            $offenders[] = "{$table} (not snake_case)";

            continue;
        }
        if (! str_ends_with($table, 's')) {
            $offenders[] = "{$table} (not plural)";
        }
    }

    expect($offenders)->toBe([], "Table names off the standard (snake_case english plural, §1):\n".implode("\n", $offenders));
});
