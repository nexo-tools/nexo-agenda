<x-app-layout>
    <x-slot:title>{{ __('Clientes') }}</x-slot:title>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold">{{ __('Clientes') }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('clients.export') }}"
               class="rounded-lg border border-primary px-3 py-2 text-sm font-medium text-primary hover:bg-primary-subtle">
                ↓ {{ __('Clientes CSV') }}
            </a>
            <a href="{{ route('bookings.export') }}"
               class="rounded-lg border border-primary px-3 py-2 text-sm font-medium text-primary hover:bg-primary-subtle">
                ↓ {{ __('Turnos CSV') }}
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('clients.index') }}" class="mt-4 max-w-sm">
        <label for="q" class="sr-only">{{ __('Buscar') }}</label>
        <input id="q" type="search" name="q" value="{{ $search }}" placeholder="{{ __('Buscar por nombre, email o teléfono…') }}"
               class="w-full rounded-lg border-control bg-surface text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
    </form>

    @if ($clients->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-line-strong p-8 text-center text-muted">
            {{ $search ? __('Sin resultados para tu búsqueda.') : __('Tus clientes aparecerán aquí con su historial cuando tengas reservas.') }}
        </div>
    @else
        <ul class="mt-6 space-y-2">
            @foreach ($clients as $client)
                <li>
                    <a href="{{ route('clients.show', ['key' => $client->key]) }}"
                       class="flex flex-wrap items-center justify-between gap-2 rounded-2xl bg-surface-raised p-4 shadow-sm hover:ring-2 hover:ring-brand-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                        <div>
                            <p class="font-semibold">
                                {{ $client->name }}
                                @if ($client->no_shows >= 2)
                                    <span class="ml-1 rounded bg-danger-subtle px-2 py-0.5 text-xs text-danger-subtle-fg" title="{{ __('No asistió :count veces', ['count' => $client->no_shows]) }}">
                                        ⚠ {{ $client->no_shows }} {{ __('no-shows') }}
                                    </span>
                                @endif
                            </p>
                            <p class="text-sm text-muted">
                                {{ $client->email ?? $client->phone ?? '—' }}
                            </p>
                        </div>
                        <p class="text-sm text-muted">
                            {{ trans_choice(':count turno|:count turnos', $client->total) }}
                            · {{ __(':count asistidos', ['count' => $client->attended]) }}
                        </p>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</x-app-layout>
