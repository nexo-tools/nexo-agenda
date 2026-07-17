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
        'admin', 'api', 'app', 'ayuda', 'blog', 'build', 'contacto', 'demo',
        'docs', 'explorar', 'help', 'icons', 'login', 'logout', 'mail', 'og',
        'password', 'privacidad', 'register', 'reservas', 'salir', 'sitemap',
        'soporte', 'status', 't', 'terminos', 'turnos', 'www',
    ],

    'attribution_url' => env('NEXO_ATTRIBUTION_URL'),
    'attribution_text' => env('NEXO_ATTRIBUTION_TEXT'),

];
