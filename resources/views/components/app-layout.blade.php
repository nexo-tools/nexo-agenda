<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-ink antialiased dark:bg-slate-900 dark:text-slate-200">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-brand-700">
            {{ __('Saltar al contenido') }}
        </a>

        <header class="border-b border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800" x-data="{ open: false }">
            <div class="mx-auto flex h-14 max-w-5xl items-center justify-between gap-4 px-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-bold">
                    <img src="/favicon.svg" alt="" width="28" height="28">
                    <span>{{ config('app.name') }}</span>
                </a>

                <nav class="hidden items-center gap-1 sm:flex" aria-label="{{ __('Principal') }}">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Agenda') }}</x-nav-link>
                    <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">{{ __('Servicios') }}</x-nav-link>
                    <x-nav-link :href="route('professionals.index')" :active="request()->routeIs('professionals.*')">{{ __('Equipo') }}</x-nav-link>
                    <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">{{ __('Clientes') }}</x-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">
                            {{ __('Salir') }}
                        </button>
                    </form>
                </nav>

                <button class="rounded-lg p-2 hover:bg-slate-100 sm:hidden dark:hover:bg-slate-700"
                        @click="open = !open" :aria-expanded="open" aria-controls="menu-movil">
                    <span class="sr-only">{{ __('Menú') }}</span>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>
            </div>

            <nav id="menu-movil" x-show="open" x-cloak class="border-t border-slate-200 px-4 py-2 sm:hidden dark:border-slate-700" aria-label="{{ __('Principal') }}">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="block w-full">{{ __('Agenda') }}</x-nav-link>
                <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')" class="block w-full">{{ __('Servicios') }}</x-nav-link>
                <x-nav-link :href="route('professionals.index')" :active="request()->routeIs('professionals.*')" class="block w-full">{{ __('Equipo') }}</x-nav-link>
                <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" class="block w-full">{{ __('Clientes') }}</x-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">
                        {{ __('Salir') }}
                    </button>
                </form>
            </nav>
        </header>

        <main id="contenido" class="mx-auto max-w-5xl px-4 py-6">
            @if (session('status'))
                <p class="mb-4 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900" role="status">{{ session('status') }}</p>
            @endif
            {{ $slot }}
        </main>
    </body>
</html>
