<x-app-layout>
    <x-slot:title>{{ $client->client_name }}</x-slot:title>

    <a href="{{ route('clients.index') }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">← {{ __('Clientes') }}</a>
    <h1 class="mt-2 text-2xl font-bold">{{ $client->client_name }}</h1>
    <p class="text-sm text-slate-600 dark:text-slate-400">
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
            <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-white px-4 py-3 text-sm shadow-sm dark:bg-slate-800">
                <span class="capitalize">
                    {{ $booking->starts_at->setTimezone($tz)->isoFormat('ddd D MMM YYYY · HH:mm') }}
                    · {{ $booking->service->name }} · {{ $booking->professional->name }}
                </span>
                <span @class([
                    'rounded px-2 py-0.5 text-xs',
                    'bg-brand-100 text-brand-900' => $booking->status === \App\Enums\BookingStatus::Confirmed,
                    'bg-emerald-100 text-emerald-900' => $booking->status === \App\Enums\BookingStatus::Attended,
                    'bg-red-100 text-red-900' => $booking->status === \App\Enums\BookingStatus::NoShow,
                    'bg-slate-200 text-slate-600' => $booking->status === \App\Enums\BookingStatus::Cancelled,
                ])>
                    {{ $booking->status->label() }}
                </span>
            </li>
        @endforeach
    </ul>
</x-app-layout>
