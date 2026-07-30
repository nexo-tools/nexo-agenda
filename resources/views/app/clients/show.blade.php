<x-app-layout>
    <x-slot:title>{{ $client->client_name }}</x-slot:title>

    <a href="{{ route('clients.index') }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">← {{ __('Clientes') }}</a>
    <h1 class="mt-2 text-2xl font-bold">{{ $client->client_name }}</h1>
    <p class="text-sm text-muted">
        {{ $client->client_email ?? '' }}
        @if ($client->client_phone)
            · {{ $client->client_phone }}
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $client->client_phone) }}"
               class="text-brand-700 hover:underline dark:text-brand-400" rel="noopener" target="_blank">✆ WhatsApp</a>
        @endif
    </p>

    <h2 class="mt-6 font-semibold">{{ __('Historial') }}</h2>
    <ul class="mt-2 space-y-2">
        @foreach ($bookings as $booking)
            <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-surface-raised px-4 py-3 text-sm shadow-sm">
                <span class="capitalize">
                    {{ $booking->starts_at->setTimezone($tz)->isoFormat('ddd D MMM YYYY · HH:mm') }}
                    · {{ $booking->service->name }} · {{ $booking->professional->name }}
                </span>
                <x-status-badge :status="$booking->status" />
            </li>
        @endforeach
    </ul>
</x-app-layout>
