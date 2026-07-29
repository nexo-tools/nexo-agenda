{{-- Shared view for privacy + terms. Content comes from lang/<locale>/legal.php;
     $title/$description reach partials.head (and so x-nexo-seo) from the controller. --}}
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

        <x-nexo-header brand="Nexo Agenda" mark="/ecosystem/nexoagenda.svg" :home="route('home')" />

        <main id="contenido" class="flex-1 px-4 py-10">
            <article class="mx-auto w-full max-w-2xl rounded-2xl bg-surface p-6 shadow-sm sm:p-8">
                <h1 class="text-2xl font-bold">{{ $content['title'] }}</h1>
                <p class="mt-1 text-xs text-muted">{{ $updated }}</p>

                <p class="mt-4 text-sm leading-relaxed text-ink">{{ $content['intro'] }}</p>

                @foreach ($content['sections'] as $section)
                    <section class="mt-6">
                        <h2 class="text-base font-semibold">{{ $section['h'] }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $section['p'] }}</p>
                    </section>
                @endforeach

                {{-- Who runs THIS instance. From env so a self-hoster does not
                     publish the upstream author's details. --}}
                @if ($operator || $contact)
                    <section class="mt-6">
                        <h2 class="text-base font-semibold">{{ __('legal.operator.h') }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-muted">
                            @if ($operator){{ __('legal.operator.p', ['operator' => $operator]) }} @endif
                            @if ($contact){{ __('legal.operator.contact', ['contact' => $contact]) }}@endif
                        </p>
                    </section>
                @endif

                <p class="mt-8 border-t border-line pt-4 text-sm">
                    <a href="{{ route('legal.privacy') }}" class="text-brand-700 underline dark:text-brand-400">{{ __('Privacidad') }}</a>
                    ·
                    <a href="{{ route('legal.terms') }}" class="text-brand-700 underline dark:text-brand-400">{{ __('Términos') }}</a>
                    ·
                    <a href="{{ route('help') }}" class="text-brand-700 underline dark:text-brand-400">{{ __('nexo.help.title') }}</a>
                </p>
            </article>
        </main>

        <x-nexo-footer />
    </body>
</html>
