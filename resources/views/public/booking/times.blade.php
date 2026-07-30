<x-public-layout :title="__('When?').' — '.$business->name" :business="$business">
    <a href="{{ route('public.professional', [$business, $service]) }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">
        ← {{ __('Change professional') }}
    </a>
    <p class="mt-3 text-sm text-muted">{{ __('Step 3 of 4') }}</p>
    <h1 class="mb-1 text-xl font-bold">{{ __('When?') }}</h1>
    <p class="mb-5 text-sm text-muted">
        {{ $service->name }} · {{ $chosen?->name ?? __('Any professional') }}
    </p>

    @if (session('slot_taken'))
        <p class="nexo-flash nexo-flash--warning mb-4" role="alert">
            {{ __('That time was just taken. Please pick another one.') }}
        </p>
    @endif

    <div class="mb-4 flex items-center justify-between gap-2">
        @if ($canGoBack)
            <a href="{{ route('public.times', [$business, $service, 'professional' => $chosen?->id ?? 'any', 'date' => $day->subDay()->toDateString()]) }}"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 dark:text-brand-400 hover:bg-bg-subtle"
               aria-label="{{ __('Previous day') }}">‹</a>
        @else
            <span class="px-3 py-2 text-sm text-line-strong" aria-hidden="true">‹</span>
        @endif

        <form method="GET" action="{{ route('public.times', [$business, $service]) }}" x-data class="flex items-center gap-1">
            <input type="hidden" name="professional" value="{{ $chosen?->id ?? 'any' }}">
            <label for="date" class="sr-only">{{ __('Date') }}</label>
            <input type="date" id="date" name="date" value="{{ $day->toDateString() }}" x-on:change="$el.form.requestSubmit()"
                   class="rounded-lg border-control bg-surface-raised text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <noscript><button class="nexo-btn nexo-btn--ghost nexo-btn--sm">{{ __('Go') }}</button></noscript>
        </form>

        @if ($canGoForward)
            <a href="{{ route('public.times', [$business, $service, 'professional' => $chosen?->id ?? 'any', 'date' => $day->addDay()->toDateString()]) }}"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 dark:text-brand-400 hover:bg-bg-subtle"
               aria-label="{{ __('Next day') }}">›</a>
        @else
            <span class="px-3 py-2 text-sm text-line-strong" aria-hidden="true">›</span>
        @endif
    </div>

    <p class="mb-3 text-center font-medium capitalize">{{ $day->isoFormat('dddd D [de] MMMM') }}</p>

    @if (session('status'))
        <p class="nexo-flash mb-4" role="status">{{ session('status') }}</p>
    @endif

    @if ($slots->isEmpty())
        <p class="rounded-2xl border border-dashed border-line-strong p-6 text-center text-sm text-muted">
            {{ __('No times available this day.') }}
        </p>
    @else
        <ul class="grid grid-cols-3 gap-2 sm:grid-cols-4">
            @foreach ($slots as $time => $professionalId)
                <li>
                    <a href="{{ route('public.form', [$business, $service, 'professional' => $professionalId, 'start' => $day->toDateString().' '.$time]) }}"
                       class="block rounded-lg bg-surface-raised py-2 text-center text-sm font-medium shadow-sm hover:ring-2 hover:ring-brand-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                        {{ $time }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <details class="mt-6 rounded-2xl bg-surface-raised p-4 shadow-sm" @if ($errors->any() || $slots->isEmpty()) open @endif>
        <summary class="cursor-pointer text-sm font-medium text-brand-700 dark:text-brand-400">
            {{ $slots->isEmpty() ? __('Notify me if a time frees up this day') : __('Prefer another time? Join the waitlist') }}
        </summary>
        <form method="POST" action="{{ route('public.waitlist', [$business, $service]) }}" class="mt-4 space-y-3">
            @csrf
            <input type="hidden" name="professional" value="{{ $chosen?->id ?? 'any' }}">
            <input type="hidden" name="date" value="{{ $day->toDateString() }}">

            <x-field :label="__('Name')" name="client_name" required autocomplete="name" />
            <x-field :label="__('Email')" name="client_email" type="email" required autocomplete="email" />
            @error('date')
                <p class="text-sm text-danger">{{ $message }}</p>
            @enderror

            <x-button>{{ __('Join the waitlist') }}</x-button>
            <p class="text-xs text-muted">
                {{ __('If someone cancels that day, we\'ll email you right away.') }}
            </p>
        </form>
    </details>
</x-public-layout>
