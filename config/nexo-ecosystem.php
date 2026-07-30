<?php

// Nexo ecosystem registry — the single source for the app-switcher and the
// footer's ecosystem links. `current` marks this tool (nexoagenda). URLs are
// env-overridable so self-hosters can point at their own instances. Marks come
// from nexo-brand/marks/<key>.svg (copied into public/ecosystem/).

return [

    // Which tool is this? The app-switcher marks it as current. One of the keys
    // in `tools` below, or null for a standalone self-host with no ecosystem
    // identity. This repo is Nexo Agenda, so it defaults to 'nexoagenda'.
    'current' => env('NEXO_ECOSYSTEM_CURRENT', 'nexoagenda'),

    // Portada (non-dev entry point) and the developer entry points.
    'hub_url' => env('NEXO_HUB_URL', 'https://nexotools.alvarocdev.com'),
    'github_org_url' => env('NEXO_GITHUB_ORG', 'https://github.com/nexo-tools'),
    'author_url' => env('NEXO_ATTRIBUTION_URL', 'https://alvarocdev.com'),

    // The tools. `status`: 'live' | 'soon'. `mark`: public path to the isotype.
    // Taglines are Spanish (the ecosystem is Spanish-first); shown as-is.
    'tools' => [
        'nexotools' => [
            'name' => 'Nexo Tools',
            'tagline' => 'El hub abierto del ecosistema Nexo.',
            'url' => env('NEXO_URL_TOOLS', 'https://nexotools.alvarocdev.com'),
            'mark' => '/ecosystem/nexotools.svg',
            'status' => 'live',
        ],
        'nexoid' => [
            'name' => 'Nexo ID',
            'tagline' => 'Una sola cuenta para todo el ecosistema Nexo.',
            'url' => env('NEXO_URL_ID', 'https://nexoid.alvarocdev.com'),
            'mark' => '/ecosystem/nexoid.svg',
            'status' => 'live',
        ],
        'nexolinks' => [
            'name' => 'Nexo Links',
            'tagline' => 'Comparte todos tus links en un solo lugar.',
            'url' => env('NEXO_URL_LINKS', 'https://nexolinks.alvarocdev.com'),
            'mark' => '/ecosystem/nexolinks.svg',
            'status' => 'live',
        ],
        'nexoagenda' => [
            'name' => 'Nexo Agenda',
            'tagline' => 'Recibe reservas para tu negocio en minutos.',
            'url' => env('NEXO_URL_AGENDA', 'https://nexoagenda.alvarocdev.com'),
            'mark' => '/ecosystem/nexoagenda.svg',
            'status' => 'live',
        ],
        'nexoshort' => [
            'name' => 'Nexo Short',
            'tagline' => 'Acorta enlaces con métricas sin cookies.',
            'url' => env('NEXO_URL_SHORT', 'https://nexoshort.alvarocdev.com'),
            'mark' => '/ecosystem/nexoshort.svg',
            'status' => 'live',
        ],
        'nexoevents' => [
            'name' => 'Nexo Events',
            'tagline' => 'Crea eventos gratis y valida entradas con QR.',
            'url' => env('NEXO_URL_EVENTS', 'https://nexoevents.alvarocdev.com'),
            'mark' => '/ecosystem/nexoevents.svg',
            'status' => 'live',
        ],
    ],
];
