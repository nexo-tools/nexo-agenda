{{-- Auth shell: login, register, password reset and the SSO onboarding form.
     Wears the same nexo-* chrome as the owner dashboard — an owner signing in is
     already inside the ecosystem, and the header is where the app-switcher and
     the locale/theme toggles live (this view used to hand-roll a wordmark and the
     legacy x-locale-switcher, so those controls disappeared exactly on the pages
     a first-time visitor sees). Private surface: noindex, like /app. --}}
@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', array_filter([
            'title' => $title,
            'noindex' => true,
        ], fn ($value) => $value !== null))
        @include('partials.beacon')
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:text-brand-700">
            {{ __('Saltar al contenido') }}
        </a>

        <x-nexo-header brand="Nexo Agenda" mark="/ecosystem/nexoagenda.svg" :home="route('home')" />

        <main id="contenido" class="flex flex-1 flex-col items-center justify-center px-4 py-10">
            <div class="w-full max-w-md rounded-2xl bg-surface p-6 shadow-sm sm:p-8">
                {{ $slot }}
            </div>
        </main>

        <x-nexo-footer />
    </body>
</html>
