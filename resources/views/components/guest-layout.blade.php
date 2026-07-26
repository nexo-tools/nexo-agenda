<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @include('partials.beacon')
    </head>
    <body class="bg-brand-50 font-sans text-ink antialiased dark:bg-slate-900 dark:text-slate-200">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-brand-700">
            {{ __('Saltar al contenido') }}
        </a>
        <main id="contenido" class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
            <a href="{{ route('home') }}" class="mb-6 flex items-center gap-3">
                <img src="/favicon.svg" alt="" width="44" height="44">
                <span class="text-2xl font-bold tracking-tight">{{ config('app.name') }}</span>
            </a>
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-sm sm:p-8 dark:bg-slate-800">
                {{ $slot }}
            </div>
            <x-locale-switcher class="mt-4" />
        </main>
    </body>
</html>
