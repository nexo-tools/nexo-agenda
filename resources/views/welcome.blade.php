<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }} — {{ __('Reservas online para tu negocio') }}</title>
        <meta name="description" content="{{ __('Agenda, servicios, profesionales y reservas online. Open source y self-hosted.') }}">

        <link rel="icon" href="/favicon.ico" sizes="48x48">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="manifest" href="/site.webmanifest">
        <meta name="theme-color" content="#7c3aed">

        <meta property="og:title" content="{{ config('app.name') }}">
        <meta property="og:description" content="{{ __('Reservas online para tu negocio') }}">
        <meta property="og:image" content="{{ url('/og-image.png') }}">
        <meta property="og:type" content="website">

        @include('partials.theme-init')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen flex-col bg-brand-50 font-sans text-ink antialiased dark:bg-slate-900 dark:text-slate-200">
        <x-nexo-header brand="Nexo Agenda" mark="/ecosystem/nexoagenda.svg" />
        <main class="flex flex-1 flex-col items-center justify-center gap-6 px-6 text-center">
            <img src="/favicon.svg" alt="" width="88" height="88">
            <h1 class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white">{{ config('app.name') }}</h1>
            <p class="max-w-md text-lg text-slate-600 dark:text-slate-400">
                {{ __('Reservas online para tu negocio. Open source, sin comisiones.') }}
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('register') }}"
                   class="rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">
                    {{ __('Crear cuenta gratis') }}
                </a>
                <a href="{{ route('directory') }}"
                   class="rounded-lg border border-brand-700 px-5 py-2.5 text-sm font-semibold text-brand-700 hover:bg-brand-50 dark:border-brand-400 dark:text-brand-400 dark:hover:bg-slate-800">
                    {{ __('Explorar negocios') }}
                </a>
            </div>
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">{{ __('Ir a mi agenda') }}</a>
            @endauth
        </main>
        <x-nexo-footer />
    </body>
</html>
