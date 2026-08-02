@php($title = __('nexo.help.title'))
@php($description = __('Frequently asked questions about bookings, cancellations and how to register your business.'))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @include('partials.beacon')
    </head>
    <body class="flex min-h-screen flex-col bg-bg font-sans text-ink antialiased">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-surface focus:px-4 focus:py-2 focus:text-brand-700">
            {{ __('Skip to content') }}
        </a>

        <x-nexo-header brand="Nexo Agenda" mark="/ecosystem/nexoagenda.svg" />

        <main id="contenido" class="flex-1">
            <div class="nexo-help">
                <h1>{{ __('nexo.help.title') }}</h1>
                <p>{{ __('nexo.help.intro') }}</p>

                @foreach ($faqs as $faq)
                    <details class="nexo-help__item">
                        <summary>{{ $faq['q'] ?? '' }}</summary>
                        <div>{!! $faq['a'] ?? '' !!}</div>
                    </details>
                @endforeach

                <div class="nexo-help__item nexo-help__contact">
                    <div>
                        <strong>{{ __('nexo.help.contact_title') }}</strong>
                        <p>
                            <a class="nexo-btn nexo-btn--primary" href="{{ $contactUrl }}">
                                {{ __('nexo.help.contact_cta') }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <x-nexo-footer />
    </body>
</html>
