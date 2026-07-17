<x-app-layout>
    <x-slot:title>{{ $professional->name }}</x-slot:title>

    <a href="{{ route('professionals.index') }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">
        ← {{ __('Equipo') }}
    </a>
    <h1 class="mt-2 text-2xl font-bold">{{ $professional->name }}</h1>

    <form method="POST" action="{{ route('professionals.update', $professional) }}" class="mt-6 max-w-2xl space-y-6"
          x-data="{
              days: @js($blocksByDay->toArray()),
              labels: { 1: @js(__('Lunes')), 2: @js(__('Martes')), 3: @js(__('Miércoles')), 4: @js(__('Jueves')), 5: @js(__('Viernes')), 6: @js(__('Sábado')), 7: @js(__('Domingo')) },
              add(d) { (this.days[d] ??= []).push({ start: '09:00', end: '18:00' }); },
              remove(d, i) { this.days[d].splice(i, 1); },
          }">
        @csrf
        @method('PUT')

        <x-field :label="__('Nombre')" name="name" :value="$professional->name" required />

        <label class="flex items-center gap-2 text-sm">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $professional->is_active))
                   class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            {{ __('Activo (puede recibir reservas)') }}
        </label>

        <fieldset>
            <legend class="mb-2 font-semibold">{{ __('Horario semanal') }}</legend>
            @error('blocks')
                <p class="mb-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <div class="space-y-3">
                <template x-for="d in [1, 2, 3, 4, 5, 6, 7]" :key="d">
                    <div class="rounded-xl bg-white p-3 shadow-sm dark:bg-slate-800">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium" x-text="labels[d]"></span>
                            <button type="button" @click="add(d)"
                                    class="rounded-lg px-2 py-1 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-700">
                                + {{ __('Franja') }}
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-slate-500" x-show="!days[d] || days[d].length === 0">{{ __('No atiende') }}</p>
                        <template x-for="(row, i) in days[d] ?? []" :key="i">
                            <div class="mt-2 flex items-center gap-2">
                                <label class="sr-only" :for="`b-${d}-${i}-s`">{{ __('Inicio') }}</label>
                                <input type="time" :id="`b-${d}-${i}-s`" x-model="row.start" :name="`blocks[${d}][${i}][start]`" required
                                       class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900">
                                <span aria-hidden="true">–</span>
                                <label class="sr-only" :for="`b-${d}-${i}-e`">{{ __('Fin') }}</label>
                                <input type="time" :id="`b-${d}-${i}-e`" x-model="row.end" :name="`blocks[${d}][${i}][end]`" required
                                       class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900">
                                <button type="button" @click="remove(d, i)"
                                        class="rounded-lg px-2 py-1 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-slate-700">
                                    <span aria-hidden="true">✕</span>
                                    <span class="sr-only">{{ __('Quitar franja') }}</span>
                                </button>
                            </div>
                        </template>
                        @foreach (range(1, 7) as $d)
                            @error('blocks.'.$d)
                                <template x-if="d === {{ $d }}">
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                </template>
                            @enderror
                        @endforeach
                    </div>
                </template>
            </div>
        </fieldset>

        <x-button class="max-w-xs">{{ __('Guardar') }}</x-button>
    </form>

    <section class="mt-10 max-w-2xl">
        <h2 class="font-semibold">{{ __('Calendario externo') }}</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ __('Suscríbete a esta URL desde Google Calendar o Apple Calendar para ver los turnos de :name automáticamente. No la compartas: cualquiera con el enlace ve la agenda.', ['name' => $professional->name]) }}
        </p>
        <div class="mt-2 flex flex-wrap items-center gap-2">
            <code class="break-all rounded-lg bg-slate-100 px-3 py-2 text-xs dark:bg-slate-800">{{ route('feeds.professional', $professional->feed_token) }}</code>
            <form method="POST" action="{{ route('professionals.feed-token', $professional) }}"
                  onsubmit="return confirm(@js(__('¿Regenerar el enlace? El actual dejará de funcionar.')))">
                @csrf
                <button class="rounded-lg px-3 py-1.5 text-sm text-brand-700 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-slate-700">
                    {{ __('Regenerar') }}
                </button>
            </form>
        </div>
    </section>

    <section class="mt-10 max-w-2xl">
        <h2 class="font-semibold">{{ __('Ausencias') }}</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('Vacaciones, feriados propios o días sin atención.') }}</p>

        <form method="POST" action="{{ route('professionals.absences.store', $professional) }}" class="mt-3 flex flex-wrap items-end gap-2">
            @csrf
            <x-field :label="__('Desde')" name="starts_on" type="date" required class="w-auto" />
            <x-field :label="__('Hasta')" name="ends_on" type="date" required class="w-auto" />
            <x-field :label="__('Motivo (opcional)')" name="reason" class="w-40" />
            <button class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                {{ __('Agregar') }}
            </button>
        </form>

        <ul class="mt-4 space-y-2">
            @foreach ($absences as $absence)
                <li class="flex items-center justify-between rounded-xl bg-white px-4 py-2 text-sm shadow-sm dark:bg-slate-800">
                    <span>
                        {{ $absence->starts_on->isoFormat('D MMM YYYY') }} – {{ $absence->ends_on->isoFormat('D MMM YYYY') }}
                        @if ($absence->reason) · {{ $absence->reason }} @endif
                    </span>
                    <form method="POST" action="{{ route('absences.destroy', $absence) }}">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-lg px-2 py-1 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-slate-700">
                            <span aria-hidden="true">✕</span>
                            <span class="sr-only">{{ __('Eliminar ausencia') }}</span>
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    </section>
</x-app-layout>
