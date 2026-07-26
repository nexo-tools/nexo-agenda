<?php

return [

    // Business categories shown in onboarding and the public directory.
    'categories' => [
        'peluqueria',
        'barberia',
        'estetica',
        'spa',
        'salud',
        'consultorio',
        'fitness',
        'educacion',
        'mascotas',
        'otro',
    ],

    // Slugs that can never be taken by a business (they collide with app routes).
    'reserved_slugs' => [
        'admin', 'api', 'app', 'auth', 'ayuda', 'blog', 'build', 'contacto', 'demo',
        'docs', 'explorar', 'help', 'icons', 'login', 'logout', 'mail', 'nexo', 'og',
        'password', 'privacidad', 'register', 'reservas', 'salir', 'sitemap',
        'soporte', 'status', 't', 'terminos', 'turnos', 'www',
    ],

    // Powered-by attribution shown in the footers. Canonical ecosystem contract
    // (same name/shape across every Nexo tool): NEXO_ATTRIBUTION_LABEL / _URL.
    'attribution' => [
        'label' => env('NEXO_ATTRIBUTION_LABEL'),
        'url' => env('NEXO_ATTRIBUTION_URL'),
    ],

    // Cookieless ecosystem analytics (opt-in). Off by default so a standalone
    // install phones nobody home; when enabled, resources/js/nexo-beacon.js
    // sendBeacon()s an anonymous pageview to the Nexo Tools hub from the owner
    // chrome only (never the public business storefront). See partials/beacon.
    'beacon' => [
        'enabled' => (bool) env('NEXO_BEACON_ENABLED', false),
        'endpoint' => (string) env('NEXO_BEACON_ENDPOINT', 'https://nexotools.alvarocdev.com/beacon'),
        'origin' => (string) env('NEXO_BEACON_ORIGIN', 'nexoagenda'),
    ],

];
