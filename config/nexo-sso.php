<?php

// NEXO_SSO_* env contract — identical across every Nexo tool (SPEC-client, nexo-id repo).
return [

    // Master switch. Off (default) = standalone mode: no SSO routes, no network. (AC-CFG-1)
    'enabled' => (bool) env('NEXO_SSO_ENABLED', false),

    // Base URL of the Nexo ID instance, e.g. https://nexoid.alvarocdev.com
    'issuer' => rtrim((string) env('NEXO_SSO_ISSUER', ''), '/'),

    // Public client id (uuid) issued by `php artisan nexo:sso-client` on the provider.
    'client_id' => (string) env('NEXO_SSO_CLIENT_ID', ''),

    // Requested scopes. openid is required for the id_token.
    'scopes' => 'openid profile email',

    // Where Nexo ID returns the browser after a central (RP-initiated) logout.
    // MUST be registered as a redirect URI of this client on the provider, or the
    // provider refuses it and shows its own "signed out" page (anti open-redirect).
    // Empty = no post-logout redirect (the provider's signed-out page is the end).
    'post_logout_redirect_uri' => (string) env('NEXO_SSO_POST_LOGOUT_REDIRECT_URI', ''),

    // Silent SSO (OIDC prompt=none): on guest pages, try once per session to
    // pick up an existing Nexo ID session with no UI and no clicks. Rides the
    // master switch; this is the per-tool kill-switch. (AC-SILENT-1)
    'silent' => (bool) env('NEXO_SSO_SILENT', true),

    // Surfaces where the silent attempt must NOT fire (public storefronts,
    // host-isolated pages, machine endpoints): path patterns (Request::is) in
    // `silent_excluded`, route names — Str::is wildcards allowed — in
    // `silent_excluded_routes` (the only way to express slug catch-alls like
    // /{username}). Adaptation point per tool. The /auth/nexo/* routes are
    // always excluded by the middleware itself.
    'silent_excluded' => [
        'up',
        'sitemap.xml',
        'robots.txt',
        'feeds/*', // .ics calendar feeds (machine clients)
        't/*',     // booking-management token links (end customers, from email)
    ],
    'silent_excluded_routes' => [
        'public.*',           // {business} booking storefronts
        'directory',
        'directory.category', // /explorar public directory
    ],

    // HTTP timeout (seconds) for every provider call — keeps degradation snappy. (AC-DEGRADE-2)
    'timeout' => (int) env('NEXO_SSO_TIMEOUT', 5),

    // Cache TTLs (seconds) for the discovery document and JWKS. (AC-CFG-2)
    'discovery_ttl' => 3600,
    'jwks_ttl' => 3600,
];
