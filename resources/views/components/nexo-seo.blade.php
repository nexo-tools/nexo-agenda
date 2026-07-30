{{-- Standard Nexo SEO head block. ONE component so every page/tool emits the same
     external metadata (title, description, canonical, OpenGraph, Twitter card,
     hreflang, theme-color, JSON-LD) — kills the per-page drift. Put it in <head>.

       <x-nexo-seo
           title="Nexo Links — Your links. Your domain. Your data."
           description="Open-source link-in-bio you host yourself…"
           image="/og-image.png"             (default; from generate-brand-assets)
           :hreflang="true"                  (emit es/en/pt + x-default via ?lang=)
           :noindex="false" />

     Assumes each tool ships /og-image.png + favicons via generate-brand-assets.mjs. --}}
@props([
    'title',
    'description',
    'image' => '/og-image.png',
    'canonical' => null,
    'type' => 'website',
    'siteName' => null,
    'hreflang' => true,               {{-- auto es/en/pt via ?lang=; or pass :locales --}}
    'locales' => null,                {{-- ['es'=>url,'en'=>url,'pt'=>url] to override --}}
    'noindex' => false,
    'jsonld' => true,
    'themeColor' => null,             {{-- local prop: a business storefront passes its own accent --}}
])
@php
    $siteName = $siteName ?: config('app.name');
    $ogImage = \Illuminate\Support\Str::startsWith($image, 'http') ? $image : url($image);
    $canonicalUrl = $canonical ?: url()->current();
    // Default hreflang set: the ecosystem standard is es/en/pt, switched via ?lang=.
    if ($hreflang && $locales === null) {
        $locales = [];
        foreach (['es', 'en', 'pt'] as $code) {
            $locales[$code] = request()->fullUrlWithQuery(['lang' => $code]);
        }
    }
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonicalUrl }}">
@if ($noindex)
    <meta name="robots" content="noindex, nofollow">
@endif
@if ($themeColor)
    {{-- A business storefront paints the browser UI with its own accent, which is
         a single colour by definition: it is the same in either scheme. --}}
    <meta name="theme-color" content="{{ $themeColor }}">
@else
    {{-- Match the page background per scheme, not the accent: the browser chrome
         should extend the page, not paint a violet bar over a dark UI.
         Values are --nexo-bg (slate-50 / slate-950); literals because <meta> can't read CSS vars. --}}
    <meta name="theme-color" content="#f8fafc" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#020617" media="(prefers-color-scheme: dark)">
@endif

<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $ogImage }}">

@if ($locales)
    @foreach ($locales as $code => $href)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $href }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $locales['en'] ?? reset($locales) }}">
@endif

@if ($jsonld && ! $noindex)
    @php
        // JSON-LD keys carry an `@` sigil, and Blade compiles `@context` as a
        // directive (Laravel 11 added one), which ships compiled PHP inside the
        // script tag instead of JSON. Keeping the sigil out of the template text
        // is what stops Blade from seeing a directive at all — and it holds if
        // Blade later claims `@type`, `@id` or `@graph` too.
        $at = '@';
    @endphp
    <script type="application/ld+json">{!! json_encode([
        $at.'context' => 'https://schema.org',
        $at.'type' => 'WebSite',
        'name' => $siteName,
        'url' => url('/'),
        'description' => $description,
        'inLanguage' => str_replace('_', '-', app()->getLocale()),
        'publisher' => [
            $at.'type' => 'Organization',
            'name' => 'Nexo',
            'url' => config('nexo-ecosystem.hub_url', 'https://nexotools.alvarocdev.com'),
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
