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

        {{-- Public storefront palette. The Nexo brand violet is the app CHROME accent
             (owner dashboard, auth, errors, marketing) — it must never reach a
             business storefront. So the public booking/directory pages keep their own
             teal accent scale here, decoupled from the chrome brand-* (violet), and a
             business overrides the primary accent with its configured color below. --}}
        <style>
            :root {
                --color-brand-50: #f0fdfa;
                --color-brand-100: #ccfbf1;
                --color-brand-200: #99f6e4;
                --color-brand-300: #5eead4;
                --color-brand-400: #2dd4bf;
                --color-brand-500: #14b8a6;
                --color-brand-600: #0d9488;
                --color-brand-700: #0f766e;
                --color-brand-800: #115e59;
                --color-brand-900: #134e4a;
            }
            @if ($business)
                @php($accent = $business->brand_color ?: '#0f766e')
                .bg-brand-700 { background-color: {{ $accent }} !important; color: {{ $business->accentTextColor() }} !important; }
                .hover\:bg-brand-800:hover { background-color: {{ $accent }} !important; filter: brightness(0.92); }
                .text-brand-700, .dark\:text-brand-400 { color: {{ $accent }} !important; }
            @endif
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
