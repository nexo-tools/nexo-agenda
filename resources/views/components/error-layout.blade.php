{{-- Shared shell for every error page. It used to be a bare document — favicon,
     app name, message — with no header and no footer, which meant an error page
     was the one surface where a person lost the theme toggle, the language
     switcher and every link out (audit 2026-08-02). A 404 is where those are
     needed more, not less. Now it wears the tool chrome like every other page.

     `code`, `title` and `message` come from the per-code views in
     resources/views/errors/, so adding a status code is still a one-liner. --}}
@props(['code', 'title', 'message'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- An error page is not a page anyone should land on from a search
             result, so it stays out of the index. --}}
        @include('partials.head', ['noindex' => true])
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:text-brand-700">
            {{ __('Skip to content') }}
        </a>

        <x-nexo-header brand="Nexo Agenda" mark="/ecosystem/nexoagenda.svg" :home="route('home')" />

        <main id="contenido" class="flex flex-1 flex-col items-center justify-center px-4 py-10 text-center">
            <p class="text-6xl font-bold tabular-nums text-brand-700 dark:text-brand-400">{{ $code }}</p>
            <h1 class="mt-4 text-2xl font-semibold">{{ $title }}</h1>
            <p class="mt-2 max-w-sm text-muted">{{ $message }}</p>

            <x-button :href="url('/')" size="inline" class="mt-8">{{ __('Back to home') }}</x-button>
        </main>

        <x-nexo-footer />
    </body>
</html>
