<x-app-layout>
    <x-slot:title>{{ __('Check-in') }}</x-slot:title>

    @php($local = $booking->starts_at->setTimezone($booking->business->timezone))

    <div class="mx-auto max-w-md rounded-2xl bg-surface-raised p-6 text-center shadow-sm">
        <h1 class="text-xl font-bold">{{ __('Check-in') }}</h1>

        <p class="mt-4 text-2xl font-bold">{{ $booking->client_name }}</p>
        <p class="mt-1 text-muted">
            {{ $booking->service->name }} · {{ $booking->professional->name }}
        </p>
        <p class="capitalize text-muted">
            {{ $local->isoFormat('dddd D MMM') }} · {{ $local->format('H:i') }}
        </p>
        <p class="mt-2">
            <x-status-badge :status="$booking->status" size="md" />
        </p>

        @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
            <form method="POST" action="{{ route('checkin.store', $token) }}" class="mt-6">
                @csrf
                <x-button><x-icon name="check" /> {{ __('Marcar como Asistió') }}</x-button>
            </form>
        @endif

        <a href="{{ route('dashboard', ['date' => $local->toDateString()]) }}"
           class="mt-4 inline-block text-sm text-brand-700 hover:underline dark:text-brand-400">
            {{ __('Ir a la agenda') }}
        </a>
    </div>
</x-app-layout>
