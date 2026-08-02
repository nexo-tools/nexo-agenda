{{-- The public landing's document scaffold. It exists so welcome.blade.php can
     be nothing but the five canonical sections: the body class that pins the
     footer to the bottom uses min-h-screen, and the family guardian (and
     STANDARD.md's anti-fingerprint grep) scans the landing view as a whole
     file. The layout owns the page, the view owns the content — the same split
     nexoshort uses. --}}
@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @include('partials.beacon')
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        {{-- Only the hero carries a primary CTA (design.md, "CTA voice"), so the
             header's action stays a ghost. --}}
        <x-nexo-header brand="Nexo Agenda" mark="/ecosystem/nexoagenda.svg">
            <x-slot:actions>
                @auth
                    <a href="{{ route('dashboard') }}" class="nexo-btn nexo-btn--ghost">{{ __('Go to my schedule') }}</a>
                @else
                    <a href="{{ route('login') }}" class="nexo-btn nexo-btn--ghost">{{ __('Sign in') }}</a>
                @endauth
            </x-slot:actions>
        </x-nexo-header>

        <main class="flex-1">
            {{ $slot }}
        </main>

        <x-nexo-footer />
    </body>
</html>
