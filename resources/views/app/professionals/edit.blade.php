<x-app-layout>
    <x-slot:title>{{ $professional->name }}</x-slot:title>

    <a href="{{ route('professionals.index') }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">
        ← {{ __('Team') }}
    </a>
    <h1 class="mt-2 text-2xl font-bold">{{ $professional->name }}</h1>

    <form method="POST" action="{{ route('professionals.update', $professional) }}" class="mt-6 max-w-2xl space-y-6"
          x-data="{
              days: @js($blocksByDay->toArray()),
              labels: { 1: @js(__('Monday')), 2: @js(__('Tuesday')), 3: @js(__('Wednesday')), 4: @js(__('Thursday')), 5: @js(__('Friday')), 6: @js(__('Saturday')), 7: @js(__('Sunday')) },
              add(d) { (this.days[d] ??= []).push({ start: '09:00', end: '18:00' }); },
              remove(d, i) { this.days[d].splice(i, 1); },
          }">
        @csrf
        @method('PUT')

        <x-field :label="__('Name')" name="name" :value="$professional->name" required />

        <label class="flex items-center gap-2 text-sm">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $professional->is_active))
                   class="rounded border-control text-brand-600 focus:ring-brand-500">
            {{ __('Active (can receive bookings)') }}
        </label>

        <fieldset>
            <legend class="mb-2 font-semibold">{{ __('Weekly schedule') }}</legend>
            @error('blocks')
                <p class="mb-2 text-sm text-danger">{{ $message }}</p>
            @enderror

            <div class="space-y-3">
                <template x-for="d in [1, 2, 3, 4, 5, 6, 7]" :key="d">
                    <div class="rounded-xl bg-surface-raised p-3 shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium" x-text="labels[d]"></span>
                            <button type="button" @click="add(d)"
                                    class="nexo-btn nexo-btn--sm text-primary hover:bg-primary-subtle">
                                + {{ __('Range') }}
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-muted" x-show="!days[d] || days[d].length === 0">{{ __('Not working') }}</p>
                        <template x-for="(row, i) in days[d] ?? []" :key="i">
                            <div class="mt-2 flex items-center gap-2">
                                <label class="sr-only" :for="`b-${d}-${i}-s`">{{ __('Start') }}</label>
                                <input type="time" :id="`b-${d}-${i}-s`" x-model="row.start" :name="`blocks[${d}][${i}][start]`" required
                                       class="rounded-lg border-control bg-surface text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                <span aria-hidden="true">–</span>
                                <label class="sr-only" :for="`b-${d}-${i}-e`">{{ __('End') }}</label>
                                <input type="time" :id="`b-${d}-${i}-e`" x-model="row.end" :name="`blocks[${d}][${i}][end]`" required
                                       class="rounded-lg border-control bg-surface text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                <button type="button" @click="remove(d, i)"
                                        class="nexo-btn nexo-btn--sm text-danger hover:bg-danger-subtle">
                                    <span aria-hidden="true">✕</span>
                                    <span class="sr-only">{{ __('Remove range') }}</span>
                                </button>
                            </div>
                        </template>
                        @foreach (range(1, 7) as $d)
                            @error('blocks.'.$d)
                                <template x-if="d === {{ $d }}">
                                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                                </template>
                            @enderror
                        @endforeach
                    </div>
                </template>
            </div>
        </fieldset>

        <x-button class="max-w-xs">{{ __('Save') }}</x-button>
    </form>

    <section class="mt-10 max-w-2xl">
        <h2 class="font-semibold">{{ __('External calendar') }}</h2>
        <p class="text-sm text-muted">
            {{ __('Subscribe to this URL from Google Calendar or Apple Calendar to see :name\'s appointments automatically. Don\'t share it: anyone with the link can see the schedule.', ['name' => $professional->name]) }}
        </p>
        <div class="mt-2 flex flex-wrap items-center gap-2">
            <code class="break-all rounded-lg bg-bg-subtle px-3 py-2 text-xs">{{ route('feeds.professional', $professional->feed_token) }}</code>
            <form method="POST" action="{{ route('professionals.feed-token', $professional) }}"
                  x-data x-on:submit="if (! confirm(@js(__('Regenerate the link? The current one will stop working.')))) $event.preventDefault()">
                @csrf
                <button class="nexo-btn nexo-btn--sm text-primary hover:bg-primary-subtle">
                    {{ __('Regenerate') }}
                </button>
            </form>
        </div>
    </section>

    <section class="mt-10 max-w-2xl">
        <h2 class="font-semibold">{{ __('Absences') }}</h2>
        <p class="text-sm text-muted">{{ __('Vacations, custom holidays or days off.') }}</p>

        <form method="POST" action="{{ route('professionals.absences.store', $professional) }}" class="mt-3 flex flex-wrap items-end gap-2">
            @csrf
            <x-field :label="__('From')" name="starts_on" type="date" required class="w-auto" />
            <x-field :label="__('Until')" name="ends_on" type="date" required class="w-auto" />
            <x-field :label="__('Reason (optional)')" name="reason" class="w-40" />
            <x-button size="inline">{{ __('Add') }}</x-button>
        </form>

        <ul class="mt-4 space-y-2">
            @foreach ($absences as $absence)
                <li class="flex items-center justify-between rounded-xl bg-surface-raised px-4 py-2 text-sm shadow-sm">
                    <span>
                        {{ $absence->starts_on->isoFormat('D MMM YYYY') }} – {{ $absence->ends_on->isoFormat('D MMM YYYY') }}
                        @if ($absence->reason) · {{ $absence->reason }} @endif
                    </span>
                    <form method="POST" action="{{ route('absences.destroy', $absence) }}">
                        @csrf
                        @method('DELETE')
                        <button class="nexo-btn nexo-btn--sm text-danger hover:bg-danger-subtle">
                            <span aria-hidden="true">✕</span>
                            <span class="sr-only">{{ __('Delete absence') }}</span>
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    </section>
</x-app-layout>
