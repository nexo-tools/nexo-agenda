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

];
