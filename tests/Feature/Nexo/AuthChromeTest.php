<?php

// Guardian: the auth screens wear the family chrome and the canonical card.
//
// Why it exists: the 2026-08-02 audit found the sign-in flow was the most
// drifted surface in the ecosystem, and the worst case was invisible to every
// existing check — nexolinks' auth pages rendered no header, no footer and no
// theme toggle at all, so a person could switch theme on a 404 but not on the
// login page they actually use. Four different cards, four different headings,
// one tool with no visible heading whatsoever.
//
// Copy into tests/Feature/Nexo/ and adjust the constant:
//
//   AUTH_ROUTES  — the auth route NAMES this tool actually registers. Routes
//                  that do not exist are skipped, not failed: nexoshort has no
//                  password reset, nexotools no email verification.
//
// Every tool wears the FULL chrome on auth — header and footer. nexoid used to
// be a documented exception (focused auth: no header on the IdP), but crossing
// from any tool's sign-in to nexoid's made the family look broken, so Alvaro
// removed the exception on 2026-08-02: six tools, one chrome, no branches.
//
// The card marker is what tells "migrated" from "not yet": until the tool's
// auth views render x-nexo-auth-card, the chrome assertions skip with a message
// instead of failing. A guardian that is red on the day it lands teaches
// everyone to ignore it (same pattern as LandingStandardTest).
//
// Pest note: toContain() is variadic — a second argument is another needle, not
// a failure message — so human-readable messages go through toBeTrue().

use Illuminate\Support\Facades\Route;

// This tool has no email verification and no confirm-password screen.
const AUTH_ROUTES = ['login', 'register', 'password.request'];
const AUTH_CARD_MARKER = 'data-nexo-auth-card';

/** Parameters for auth routes that take one, e.g. ['password.reset' => ['token' => 'x']]. */
const ROUTE_PARAMETERS = [];

/** The auth routes this tool actually registers, as name => rendered HTML. */
function authPages(): array
{
    $pages = [];

    foreach (AUTH_ROUTES as $name) {
        if (! Route::has($name)) {
            continue;
        }

        // A screen reached by a signed link (reset-password/{token}) cannot be
        // generated without one; the placeholder is enough to render the form.
        $response = test()->get(route($name, ROUTE_PARAMETERS[$name] ?? []));

        // A tool can gate a screen behind a signed-in user (confirm-password) or
        // a signed-out one; a redirect is a legitimate answer and not this
        // guardian's business. Only what renders is inspected.
        if ($response->status() !== 200) {
            continue;
        }

        $pages[$name] = $response->getContent();
    }

    return $pages;
}

it('registers at least one auth screen to guard', function () {
    expect(authPages())->not->toBeEmpty(
        'None of AUTH_ROUTES rendered — adjust the constant to this tool\'s real auth route names.'
    );
});

it('sits every auth screen in the canonical card', function () {
    foreach (authPages() as $name => $html) {
        expect(str_contains($html, AUTH_CARD_MARKER))->toBeTrue(
            "Route [{$name}] does not render x-nexo-auth-card — copy templates/nexo-ui/components/nexo-auth-card.blade.php and use it in the auth layout."
        );
    }
});

it('wraps the auth screens in the shared chrome', function () {
    foreach (authPages() as $name => $html) {
        if (! str_contains($html, AUTH_CARD_MARKER)) {
            test()->markTestSkipped("Auth not yet migrated to the canonical card ({$name}) — see STANDARD.md \"Auth y errores\".");
        }

        expect(str_contains($html, 'nexo-header'))->toBeTrue("Route [{$name}] does not render x-nexo-header.");
        expect(str_contains($html, 'nexo-footer'))->toBeTrue("Route [{$name}] does not render x-nexo-footer.");
    }
});

it('gives every auth screen one visible heading', function () {
    foreach (authPages() as $name => $html) {
        if (! str_contains($html, AUTH_CARD_MARKER)) {
            test()->markTestSkipped("Auth not yet migrated to the canonical card ({$name}) — see STANDARD.md \"Auth y errores\".");
        }

        // "Which page am I on" cannot live in the <title> alone: nexolinks'
        // auth views had no <h1> at all, so a screen reader landed on a form
        // with no announced purpose.
        expect(preg_match_all('/<h1[\s>]/i', $html))->toBe(
            1,
            "Route [{$name}] must render exactly one <h1> (the screen's purpose), either via the card's :title or in the view."
        );
    }
});
