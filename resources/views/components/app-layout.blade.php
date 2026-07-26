<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @include('partials.beacon')
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:text-brand-700">
            {{ __('Saltar al contenido') }}
        </a>

        <div x-data="{ open: false }">
            <x-nexo-header brand="Nexo Agenda" mark="/ecosystem/nexoagenda.svg" :home="route('dashboard')">
                <x-slot:nav>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Agenda') }}</x-nav-link>
                    <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">{{ __('Servicios') }}</x-nav-link>
                    <x-nav-link :href="route('professionals.index')" :active="request()->routeIs('professionals.*')">{{ __('Equipo') }}</x-nav-link>
                    <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">{{ __('Clientes') }}</x-nav-link>
                    <x-nav-link :href="route('stats')" :active="request()->routeIs('stats')">{{ __('Estadísticas') }}</x-nav-link>
                    <x-nav-link :href="route('reviews.index')" :active="request()->routeIs('reviews.*')">{{ __('Reseñas') }}</x-nav-link>
                    <x-nav-link :href="route('settings.edit')" :active="request()->routeIs('settings.*')">{{ __('Ajustes') }}</x-nav-link>
                </x-slot:nav>

                <x-slot:actions>
                    {{-- Owner account action (desktop); on mobile it lives in the panel below. --}}
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                        @csrf
                        <button class="nexo-btn nexo-btn--ghost">{{ __('Salir') }}</button>
                    </form>

                    {{-- Primary-nav toggle (mobile only); the nexo-header nav is hidden below md. --}}
                    <button type="button" class="nexo-btn nexo-btn--ghost nexo-btn--icon md:hidden"
                            @click="open = !open" :aria-expanded="open" aria-controls="menu-movil">
                        <span class="sr-only">{{ __('Menú') }}</span>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <path d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                    </button>
                </x-slot:actions>
            </x-nexo-header>

            <nav id="menu-movil" x-show="open" x-cloak class="border-b border-line bg-surface px-4 py-2 md:hidden" aria-label="{{ __('Principal') }}">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="block w-full">{{ __('Agenda') }}</x-nav-link>
                <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')" class="block w-full">{{ __('Servicios') }}</x-nav-link>
                <x-nav-link :href="route('professionals.index')" :active="request()->routeIs('professionals.*')" class="block w-full">{{ __('Equipo') }}</x-nav-link>
                <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" class="block w-full">{{ __('Clientes') }}</x-nav-link>
                <x-nav-link :href="route('stats')" :active="request()->routeIs('stats')" class="block w-full">{{ __('Estadísticas') }}</x-nav-link>
                <x-nav-link :href="route('reviews.index')" :active="request()->routeIs('reviews.*')" class="block w-full">{{ __('Reseñas') }}</x-nav-link>
                <x-nav-link :href="route('settings.edit')" :active="request()->routeIs('settings.*')" class="block w-full">{{ __('Ajustes') }}</x-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">
                        {{ __('Salir') }}
                    </button>
                </form>
            </nav>
        </div>

        <main id="contenido" class="mx-auto w-full max-w-5xl flex-1 px-4 py-6">
            @if (session('status'))
                <p class="mb-4 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900" role="status">{{ session('status') }}</p>
            @endif
            {{ $slot }}
        </main>

        <x-nexo-footer />
    </body>
</html>
