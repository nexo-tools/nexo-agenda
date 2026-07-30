{{-- The home used to inline its own head because it was the only page on
     x-nexo-seo; now every page goes through partials.head, so it does too. --}}
@php($title = __('Online bookings for your business'))
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
                    <a href="{{ route('dashboard') }}" class="nexo-btn nexo-btn--ghost">{{ __('Go to my schedule') }}</a>
                @else
                    <a href="{{ route('login') }}" class="nexo-btn nexo-btn--ghost">{{ __('Log in') }}</a>
                @endauth
            </x-slot:actions>
        </x-nexo-header>
        {{-- Not a viewport-height centred hero: somebody deciding whether to open
             an account needs to see what the product does, and the three steps
             below are the real onboarding flow, not a marketing invention. --}}
        <main class="mx-auto w-full max-w-3xl flex-1 px-6 py-12">
            <h1 class="max-w-2xl text-4xl font-bold tracking-tight">
                {{ __('Online booking for your business, no commissions') }}
            </h1>
            <p class="mt-4 max-w-xl text-lg text-muted">
                {{ __('Your clients book on their own from a page with your services, your team and your hours. Open source: you can host it yourself.') }}
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <x-button :href="route('register')" size="inline">{{ __('Create a free account') }}</x-button>
                <x-button :href="route('directory')" size="inline" variant="outline">{{ __('Explore businesses') }}</x-button>
            </div>
            @auth
                <p class="mt-4">
                    <a href="{{ route('dashboard') }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">{{ __('Go to my schedule') }}</a>
                </p>
            @endauth

            <section class="mt-14" aria-labelledby="como-funciona">
                <h2 id="como-funciona" class="text-sm font-semibold uppercase tracking-wide text-muted">{{ __('How it works') }}</h2>
                <ol class="mt-4 grid gap-4 sm:grid-cols-3">
                    @foreach ([
                        [__('Add your services'), __('Duration, price and each professional\'s hours.')],
                        [__('Share your link'), __('Your public page lives at :url', ['url' => url('/').'/tu-negocio'])],
                        [__('Get bookings'), __('Confirmation and reminder by email; you see them in your schedule.')],
                    ] as $index => [$stepTitle, $stepText])
                        <li class="rounded-2xl border border-line p-4">
                            <span class="text-sm font-semibold text-brand-700 dark:text-brand-400">{{ $index + 1 }}</span>
                            <h3 class="mt-1 font-semibold">{{ $stepTitle }}</h3>
                            <p class="mt-1 text-sm text-muted">{{ $stepText }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>

            <section class="mt-12 rounded-2xl bg-surface p-6 shadow-sm" aria-labelledby="incluye">
                <h2 id="incluye" class="font-semibold">{{ __('What\'s already included') }}</h2>
                <ul class="mt-3 grid gap-2 text-sm text-muted sm:grid-cols-2">
                    @foreach ([
                        __('Per-professional schedule, by day and by week'),
                        __('Waitlist when no slots are left'),
                        __('Reviews from people who actually showed up'),
                        __('Counter mode for the front desk'),
                        __('Visit and occupancy stats'),
                        __('Client and booking export to CSV'),
                    ] as $feature)
                        <li class="flex items-start gap-2">
                            <x-icon name="check" class="mt-0.5 text-brand-700 dark:text-brand-400" />
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </section>
        </main>
        <x-nexo-footer />
    </body>
</html>
