<x-app-layout>
    <x-slot:title>{{ __('Equipo') }}</x-slot:title>

    <h1 class="text-2xl font-bold">{{ __('Equipo') }}</h1>

    <form method="POST" action="{{ route('professionals.store') }}" class="mt-4 flex max-w-md gap-2">
        @csrf
        <div class="flex-1">
            <label for="name" class="sr-only">{{ __('Nombre del profesional') }}</label>
            <input id="name" name="name" required placeholder="{{ __('Nombre del profesional') }}"
                   class="w-full rounded-lg border-slate-300 bg-white text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
            @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <button class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
            {{ __('Agregar') }}
        </button>
    </form>

    @if ($professionals->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-slate-700">
            {{ __('Agrega a las personas que atienden turnos. Si trabajas en solitario, agrégate a ti.') }}
        </div>
    @else
        <ul class="mt-6 space-y-3">
            @foreach ($professionals as $professional)
                <li class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                    <div>
                        <p class="font-semibold">
                            {{ $professional->name }}
                            @unless ($professional->is_active)
                                <span class="ml-1 rounded bg-slate-200 px-2 py-0.5 text-xs text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ __('Inactivo') }}</span>
                            @endunless
                        </p>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            @if ($professional->schedule_blocks_count > 0)
                                {{ trans_choice(':count franja horaria|:count franjas horarias', $professional->schedule_blocks_count) }}
                            @else
                                {{ __('Sin horarios definidos aún') }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('professionals.edit', $professional) }}"
                           class="rounded-lg px-3 py-1.5 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-700">
                            {{ __('Horarios y datos') }}
                        </a>
                        <form method="POST" action="{{ route('professionals.destroy', $professional) }}"
                              onsubmit="return confirm(@js(__('¿Eliminar este profesional?')))">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-lg px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-slate-700">
                                {{ __('Eliminar') }}
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</x-app-layout>
