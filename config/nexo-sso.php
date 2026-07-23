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

    // HTTP timeout (seconds) for every provider call — keeps degradation snappy. (AC-DEGRADE-2)
    'timeout' => (int) env('NEXO_SSO_TIMEOUT', 5),

    // Cache TTLs (seconds) for the discovery document and JWKS. (AC-CFG-2)
    'discovery_ttl' => 3600,
    'jwks_ttl' => 3600,
];
