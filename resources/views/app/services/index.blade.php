<x-app-layout>
    <x-slot:title>{{ __('Servicios') }}</x-slot:title>

    <div class="flex items-center justify-between gap-4">
        <h1 class="text-2xl font-bold">{{ __('Servicios') }}</h1>
        <a href="{{ route('services.create') }}"
           class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
            {{ __('Nuevo servicio') }}
        </a>
    </div>

    @if ($services->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-line-strong p-8 text-center text-muted">
            {{ __('Todavía no tienes servicios. Crea el primero para empezar a recibir reservas.') }}
        </div>
    @else
        <ul class="mt-6 space-y-3">
            @foreach ($services as $service)
                <li class="rounded-2xl bg-surface-raised p-4 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold">
                                {{ $service->name }}
                                @unless ($service->is_active)
                                    <span class="ml-1 rounded bg-bg-subtle px-2 py-0.5 text-xs text-muted">{{ __('Inactivo') }}</span>
                                @endunless
                            </p>
                            <p class="text-sm text-muted">
                                {{ $service->duration_minutes }} min
                                · {{ $service->mode->label() }}
                                @if ($service->price !== null)
                                    · ${{ number_format((float) $service->price, 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('services.edit', $service) }}"
                               class="rounded-lg px-3 py-1.5 text-sm text-primary hover:bg-primary-subtle">
                                {{ __('Editar') }}
                            </a>
                            <form method="POST" action="{{ route('services.destroy', $service) }}"
                                  x-data x-on:submit="if (! confirm(@js(__('¿Eliminar este servicio?')))) $event.preventDefault()">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg px-3 py-1.5 text-sm text-danger hover:bg-danger-subtle">
                                    {{ __('Eliminar') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</x-app-layout>
