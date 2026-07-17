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
        <div class="mt-8 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-slate-700">
            {{ __('Todavía no tienes servicios. Crea el primero para empezar a recibir reservas.') }}
        </div>
    @else
        <ul class="mt-6 space-y-3">
            @foreach ($services as $service)
                <li class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold">
                                {{ $service->name }}
                                @unless ($service->is_active)
                                    <span class="ml-1 rounded bg-slate-200 px-2 py-0.5 text-xs text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ __('Inactivo') }}</span>
                                @endunless
                            </p>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                {{ $service->duration_minutes }} min
                                · {{ $service->mode->label() }}
                                @if ($service->price !== null)
                                    · ${{ number_format((float) $service->price, 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('services.edit', $service) }}"
                               class="rounded-lg px-3 py-1.5 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-700">
                                {{ __('Editar') }}
                            </a>
                            <form method="POST" action="{{ route('services.destroy', $service) }}"
                                  onsubmit="return confirm(@js(__('¿Eliminar este servicio?')))">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-slate-700">
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
