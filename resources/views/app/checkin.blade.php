<x-app-layout>
    <x-slot:title>{{ __('Check-in') }}</x-slot:title>

    @php($local = $booking->starts_at->setTimezone($booking->business->timezone))

    <div class="mx-auto max-w-md rounded-2xl bg-white p-6 text-center shadow-sm dark:bg-slate-800">
        <h1 class="text-xl font-bold">{{ __('Check-in') }}</h1>

        <p class="mt-4 text-2xl font-bold">{{ $booking->client_name }}</p>
        <p class="mt-1 text-slate-600 dark:text-slate-400">
            {{ $booking->service->name }} · {{ $booking->professional->name }}
        </p>
        <p class="capitalize text-slate-600 dark:text-slate-400">
            {{ $local->isoFormat('dddd D MMM') }} · {{ $local->format('H:i') }}
        </p>
        <p class="mt-2">
            <span @class([
                'rounded px-2 py-1 text-sm',
                'bg-brand-100 text-brand-900' => $booking->status === \App\Enums\BookingStatus::Confirmed,
                'bg-emerald-100 text-emerald-900' => $booking->status === \App\Enums\BookingStatus::Attended,
                'bg-red-100 text-red-900' => $booking->status === \App\Enums\BookingStatus::NoShow,
                'bg-slate-200 text-slate-600' => $booking->status === \App\Enums\BookingStatus::Cancelled,
            ])>
                {{ $booking->status->label() }}
            </span>
        </p>

        @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
            <form method="POST" action="{{ route('checkin.store', $token) }}" class="mt-6">
                @csrf
                <x-button>✓ {{ __('Marcar como Asistió') }}</x-button>
            </form>
        @endif

        <a href="{{ route('dashboard', ['date' => $local->toDateString()]) }}"
           class="mt-4 inline-block text-sm text-brand-700 hover:underline dark:text-brand-400">
            {{ __('Ir a la agenda') }}
        </a>
    </div>
</x-app-layout>
