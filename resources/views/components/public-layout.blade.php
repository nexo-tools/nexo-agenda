{{-- Public storefront shell: business page, booking funnel, directory, contact.
     $title/$description/$noindex are declared props on purpose — an attribute a
     component does not declare never becomes a variable, so before this every
     storefront page silently rendered the generic site title and no SEO block. --}}
@props([
    'business' => null,
    'title' => null,
    'description' => null,
    'noindex' => false,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', array_filter([
            'title' => $title,
            'description' => $description,
            'noindex' => $noindex,
            'themeColor' => $business?->brand_color ?: '#0f766e',
        ], fn ($value) => $value !== null))

        {{-- Public storefront accent. The Nexo brand violet is the app CHROME accent
             (owner dashboard, auth, errors, marketing) — it must never reach a
             business storefront, so the whole brand-* scale is re-derived here from
             one accent: teal by default, the business's configured colour when it
             has one.

             Redefining the scale variables, not overriding a handful of utility
             classes: the previous version patched .bg-brand-700 / .text-brand-700
             with !important, so everything else on the scale (the slot hover ring,
             the tinted chips) stayed teal and a single booking step showed two
             accents at once. It also forced the light accent into dark mode, where
             a dark accent like #134e4a rendered unreadable on a dark page. --}}
        @php($accent = $business?->brand_color ?: '#0f766e')
        <style>
            :root {
                --accent: {{ $accent }};
                --color-brand-fg: {{ $business?->accentTextColor() ?? '#ffffff' }};
                --color-brand-50: color-mix(in srgb, var(--accent) 8%, #fff);
                --color-brand-100: color-mix(in srgb, var(--accent) 16%, #fff);
                --color-brand-200: color-mix(in srgb, var(--accent) 30%, #fff);
                --color-brand-300: color-mix(in srgb, var(--accent) 45%, #fff);
                --color-brand-400: color-mix(in srgb, var(--accent) 62%, #fff);
                --color-brand-500: color-mix(in srgb, var(--accent) 80%, #fff);
                --color-brand-600: color-mix(in srgb, var(--accent) 92%, #fff);
                --color-brand-700: var(--accent);
                --color-brand-800: color-mix(in srgb, var(--accent) 88%, #000);
                --color-brand-900: color-mix(in srgb, var(--accent) 72%, #000);
            }
            {{-- Dark: the accent moves up the scale instead of staying put. Text and
                 borders take the lightened tints (brand-300/400, what dark:text-brand-400
                 already asks for) and the tinted fills go down to brand-900. --}}
            :root[data-theme="dark"] {
                --color-brand-50: color-mix(in srgb, var(--accent) 22%, #000);
                --color-brand-100: color-mix(in srgb, var(--accent) 30%, #000);
                --color-brand-200: color-mix(in srgb, var(--accent) 45%, #000);
                --color-brand-300: color-mix(in srgb, var(--accent) 50%, #fff);
                --color-brand-400: color-mix(in srgb, var(--accent) 62%, #fff);
                --color-brand-500: color-mix(in srgb, var(--accent) 74%, #fff);
                --color-brand-600: color-mix(in srgb, var(--accent) 86%, #fff);
                --color-brand-800: color-mix(in srgb, var(--accent) 40%, #000);
                --color-brand-900: color-mix(in srgb, var(--accent) 26%, #000);
            }
        </style>
    </head>
    <body class="min-h-screen bg-bg font-sans text-ink antialiased">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:text-brand-700">
            {{ __('Saltar al contenido') }}
        </a>

        <main id="contenido" class="mx-auto max-w-xl px-4 py-6">
            {{ $slot }}
        </main>

        <footer class="mx-auto max-w-xl px-4 pb-8 text-center text-xs text-muted">
            <x-locale-switcher class="mb-3 justify-center" />
            {{-- The storefront carries no ecosystem chrome (a business page is the
                 business's, not Nexo's), so the legal links the standard puts in
                 nexo-footer have to be repeated here — and this is exactly where
                 they matter, since this is where a client leaves their data. --}}
            <nav class="mb-3 flex flex-wrap justify-center gap-x-4 gap-y-1" aria-label="{{ __('Enlaces del sitio') }}">
                <a href="{{ route('help') }}" class="hover:underline">{{ __('nexo.help.title') }}</a>
                <a href="{{ route('contact') }}" class="hover:underline">{{ __('Contacto') }}</a>
                <a href="{{ route('legal.privacy') }}" class="hover:underline">{{ __('Privacidad') }}</a>
                <a href="{{ route('legal.terms') }}" class="hover:underline">{{ __('Términos') }}</a>
            </nav>
            @if (config('nexo.attribution.label'))
                <a href="{{ config('nexo.attribution.url') ?: url('/') }}" class="hover:underline" rel="noopener">
                    {{ config('nexo.attribution.label') }}
                </a>
            @else
                <a href="{{ url('/') }}" class="hover:underline">{{ config('app.name') }}</a>
            @endif
        </footer>
    </body>
</html>
