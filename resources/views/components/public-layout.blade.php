@props(['business' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @isset($meta){{ $meta }}@endisset

        @if ($business?->brand_color)
            <style>
                .bg-brand-700 { background-color: {{ $business->brand_color }} !important; color: {{ $business->accentTextColor() }} !important; }
                .hover\:bg-brand-800:hover { background-color: {{ $business->brand_color }} !important; filter: brightness(0.9); }
                .text-brand-700 { color: {{ $business->brand_color }} !important; }
                .dark .dark\:text-brand-400 { color: {{ $business->brand_color }} !important; }
            </style>
        @endif
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-ink antialiased dark:bg-slate-900 dark:text-slate-200">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-brand-700">
            {{ __('Saltar al contenido') }}
        </a>

        <main id="contenido" class="mx-auto max-w-xl px-4 py-6">
            {{ $slot }}
        </main>

        <footer class="mx-auto max-w-xl px-4 pb-8 text-center text-xs text-slate-500 dark:text-slate-400">
            <x-locale-switcher class="mb-3 justify-center" />
            <nav class="mb-3 flex justify-center gap-4" aria-label="{{ __('Ayuda') }}">
                <a href="{{ route('help') }}" class="hover:underline">{{ __('Ayuda') }}</a>
                <a href="{{ route('contact') }}" class="hover:underline">{{ __('Contacto') }}</a>
            </nav>
            @if (config('nexo.attribution_text'))
                <a href="{{ config('nexo.attribution_url') ?: url('/') }}" class="hover:underline" rel="noopener">
                    {{ config('nexo.attribution_text') }}
                </a>
            @else
                <a href="{{ url('/') }}" class="hover:underline">{{ config('app.name') }}</a>
            @endif
        </footer>
    </body>
</html>
