@props(['code', 'title', 'message'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- An error page is not a page anyone should land on from a search
             result, so it stays out of the index. --}}
        @include('partials.head', ['noindex' => true])
    </head>
    <body class="bg-bg font-sans text-ink antialiased">
        <main class="flex min-h-screen flex-col items-center justify-center px-4 py-10 text-center">
            <a href="{{ url('/') }}" class="mb-8 flex items-center gap-3">
                <img src="/favicon.svg" alt="" width="40" height="40">
                <span class="text-xl font-bold tracking-tight">{{ config('app.name') }}</span>
            </a>

            <p class="text-6xl font-bold tabular-nums text-brand-700 dark:text-brand-400">{{ $code }}</p>
            <h1 class="mt-4 text-2xl font-semibold">{{ $title }}</h1>
            <p class="mt-2 max-w-sm text-muted">{{ $message }}</p>

            <x-button :href="url('/')" size="inline" class="mt-8">{{ __('Volver al inicio') }}</x-button>
        </main>
    </body>
</html>
