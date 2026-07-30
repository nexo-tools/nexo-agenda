{{-- The home used to inline its own head because it was the only page on
     x-nexo-seo; now every page goes through partials.head, so it does too. --}}
@php($title = __('Reservas online para tu negocio'))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @include('partials.beacon')
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <x-nexo-header brand="Nexo Agenda" mark="/ecosystem/nexoagenda.svg">
            <x-slot:actions>
                @auth
                    <a href="{{ route('dashboard') }}" class="nexo-btn nexo-btn--ghost">{{ __('Ir a mi agenda') }}</a>
                @else
                    <a href="{{ route('login') }}" class="nexo-btn nexo-btn--ghost">{{ __('Entrar') }}</a>
                @endauth
            </x-slot:actions>
        </x-nexo-header>
        <main class="flex flex-1 flex-col items-center justify-center gap-6 px-6 text-center">
            <img src="/favicon.svg" alt="" width="88" height="88">
            <h1 class="text-4xl font-bold tracking-tight text-ink">{{ config('app.name') }}</h1>
            <p class="max-w-md text-lg text-muted">
                {{ __('Reservas online para tu negocio. Open source, sin comisiones.') }}
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <x-button :href="route('register')" size="inline">{{ __('Crear cuenta gratis') }}</x-button>
                <x-button :href="route('directory')" size="inline" variant="outline">{{ __('Explorar negocios') }}</x-button>
            </div>
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">{{ __('Ir a mi agenda') }}</a>
            @endauth
        </main>
        <x-nexo-footer />
    </body>
</html>
