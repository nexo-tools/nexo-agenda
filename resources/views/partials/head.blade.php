<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

{{-- One component emits title/description/canonical/OG/twitter/hreflang/JSON-LD
     for every page (nexo-ui standard). Before this, only the home used it and the
     indexable public pages hand-rolled a <meta name="description"> and nothing
     else. Views set $title/$description (and $noindex on the private ones) before
     including this partial; a layout has to forward them as declared props,
     because attributes it does not declare never become variables here.
     Chrome pages get the Nexo violet; the storefront passes the business accent
     so its mobile UI never turns violet. --}}
<x-nexo-seo
    :title="isset($title) ? $title.' — '.config('app.name') : config('app.name')"
    :description="$description ?? __('Schedule, services, professionals and online bookings. Open source and self-hosted.')"
    :image="$seoImage ?? '/og-image.png'"
    :noindex="$noindex ?? false"
    :jsonld="($jsonld ?? true) && ! ($noindex ?? false)"
    :theme-color="$themeColor ?? null" />

<link rel="icon" href="/favicon.ico" sizes="48x48">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">

@include('partials.theme-init')

{{-- @vite builds the woff2 files but never asks for them: the @font-face rules
     only ship if Vite::fonts() emits them. Without this line the family face is
     published to public/build/assets and nobody requests it, so every page falls
     back to the system stack. It goes before @vite so the face is known when the
     CSS lands. --}}
{{ Vite::fonts() }}

@vite(['resources/css/app.css', 'resources/js/app.js'])
