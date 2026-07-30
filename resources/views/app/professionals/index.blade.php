<x-app-layout>
    <x-slot:title>{{ __('Team') }}</x-slot:title>

    <h1 class="text-2xl font-bold">{{ __('Team') }}</h1>

    <form method="POST" action="{{ route('professionals.store') }}" class="mt-4 flex max-w-md gap-2">
        @csrf
        <div class="flex-1">
            <label for="name" class="sr-only">{{ __('Professional\'s name') }}</label>
            <input id="name" name="name" required placeholder="{{ __('Professional\'s name') }}"
                   class="w-full rounded-lg border-control bg-surface text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500">
            @error('name')
                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>
        <x-button size="inline">{{ __('Add') }}</x-button>
    </form>

    @if ($professionals->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-line-strong p-8 text-center text-muted">
            {{ __('Add the people who take appointments. If you work solo, add yourself.') }}
        </div>
    @else
        <ul class="mt-6 space-y-3">
            @foreach ($professionals as $professional)
                <li class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-surface-raised p-4 shadow-sm">
                    <div>
                        <p class="font-semibold">
                            {{ $professional->name }}
                            @unless ($professional->is_active)
                                <span class="ml-1 rounded bg-bg-subtle px-2 py-0.5 text-xs text-muted">{{ __('Inactive') }}</span>
                            @endunless
                        </p>
                        <p class="text-sm text-muted">
                            @if ($professional->schedule_blocks_count > 0)
                                {{ trans_choice(':count time range|:count time ranges', $professional->schedule_blocks_count) }}
                            @else
                                {{ __('No schedule set yet') }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('professionals.edit', $professional) }}"
                           class="nexo-btn nexo-btn--sm text-primary hover:bg-primary-subtle">
                            {{ __('Schedule & details') }}
                        </a>
                        <form method="POST" action="{{ route('professionals.destroy', $professional) }}"
                              x-data x-on:submit="if (! confirm(@js(__('Delete this professional?')))) $event.preventDefault()">
                            @csrf
                            @method('DELETE')
                            <button class="nexo-btn nexo-btn--sm text-danger hover:bg-danger-subtle">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</x-app-layout>
