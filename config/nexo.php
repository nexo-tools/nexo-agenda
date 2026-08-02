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
        'label' => env('NEXO_ATTRIBUTION_LABEL', 'made with Nexo Agenda'),
        'url' => env('NEXO_ATTRIBUTION_URL'),
    ],

    // Who runs THIS instance, named on the legal pages. Env-backed for the same
    // reason as the attribution above: a third party that clones the repo must
    // not publish the upstream author as the data controller. Left empty, the
    // legal pages simply point at the contact form instead of naming anyone.
    // Mail al operador cuando algo revienta (nexo-ops). Off por default: una
    // instancia recién clonada no debería empezar a mandar correo sin que su
    // operador lo decida. Dedupe de 15 min por excepción, kill-switch por env.
    'ops_mail' => env('NEXO_OPS_MAIL', false),

    // A dónde van esos avisos. Esta tool no tenía la clave: su canal de contacto
    // con el usuario es el formulario interno, pero el operador necesita una
    // dirección propia para los errores.
    'support_email' => env('NEXO_SUPPORT_EMAIL', 'hola@example.com'),

    'legal' => [
        'operator' => env('NEXO_LEGAL_OPERATOR'),
        'contact' => env('NEXO_LEGAL_CONTACT'),
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
