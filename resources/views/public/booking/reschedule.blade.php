{{-- noindex, same reason as the manage page: the token lives in the URL. --}}
<x-public-layout :title="__('Reprogramar').' — '.$booking->business->name" :business="$booking->business" :noindex="true">
    <a href="{{ route('booking.manage', $token) }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">
        ← {{ __('Tu turno') }}
    </a>
    <h1 class="mb-1 mt-3 text-xl font-bold">{{ __('Reprogramar turno') }}</h1>
    <p class="mb-5 text-sm text-muted">
        {{ $booking->service->name }} · {{ $booking->professional->name }}
    </p>

    @if (session('slot_taken'))
        <p class="nexo-flash nexo-flash--warning mb-4" role="alert">
            {{ __('Ese horario acaba de ocuparse. Elige otro, por favor.') }}
        </p>
    @endif

    <div class="mb-4 flex items-center justify-between gap-2">
        @if ($canGoBack)
            <a href="{{ route('booking.reschedule', [$token, 'date' => $day->subDay()->toDateString()]) }}"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 dark:text-brand-400 hover:bg-bg-subtle"
               aria-label="{{ __('Día anterior') }}">‹</a>
        @else
            <span class="px-3 py-2 text-sm text-line-strong" aria-hidden="true">‹</span>
        @endif

        <form method="GET" action="{{ route('booking.reschedule', $token) }}" x-data class="flex items-center gap-1">
            <label for="date" class="sr-only">{{ __('Fecha') }}</label>
            <input type="date" id="date" name="date" value="{{ $day->toDateString() }}" x-on:change="$el.form.requestSubmit()"
                   class="rounded-lg border-control bg-surface-raised text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <noscript><button class="nexo-btn nexo-btn--ghost nexo-btn--sm">{{ __('Ir') }}</button></noscript>
        </form>

        @if ($canGoForward)
            <a href="{{ route('booking.reschedule', [$token, 'date' => $day->addDay()->toDateString()]) }}"
               class="rounded-lg px-3 py-2 text-sm text-brand-700 dark:text-brand-400 hover:bg-bg-subtle"
               aria-label="{{ __('Día siguiente') }}">›</a>
        @else
            <span class="px-3 py-2 text-sm text-line-strong" aria-hidden="true">›</span>
        @endif
    </div>

    <p class="mb-3 text-center font-medium capitalize">{{ $day->isoFormat('dddd D [de] MMMM') }}</p>

    @if ($slots->isEmpty())
        <p class="rounded-2xl border border-dashed border-line-strong p-6 text-center text-sm text-muted">
            {{ __('No hay horarios disponibles este día.') }}
        </p>
    @else
        <ul class="grid grid-cols-3 gap-2 sm:grid-cols-4">
            @foreach ($slots as $slot)
                <li>
                    <form method="POST" action="{{ route('booking.reschedule.update', $token) }}">
                        @csrf
                        <input type="hidden" name="start" value="{{ $slot->format('Y-m-d H:i') }}">
                        <button class="w-full rounded-lg bg-surface-raised py-2 text-center text-sm font-medium shadow-sm hover:ring-2 hover:ring-brand-500">
                            {{ $slot->format('H:i') }}
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</x-public-layout>
