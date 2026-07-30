@php($service = $service ?? null)

<div class="space-y-4" x-data="{ mode: '{{ old('mode', $service?->mode->value ?? 'in_person') }}' }">
    <x-field :label="__('Service name')" name="name" :value="$service?->name" required />

    <div class="grid grid-cols-2 gap-4">
        <x-field :label="__('Duration (minutes)')" name="duration_minutes" type="number"
                 :value="$service?->duration_minutes ?? 30" required min="5" max="600" step="5" />
        <x-field :label="__('Price (optional)')" name="price" type="number"
                 :value="$service?->price === null ? null : (float) $service->price" min="0" step="0.01" />
    </div>

    <x-select :label="__('Mode')" name="mode"
              :options="collect(\App\Enums\ServiceMode::cases())->mapWithKeys(fn ($m) => [$m->value => $m->label()])"
              :selected="$service?->mode->value" x-model="mode" />

    <div x-show="mode === 'virtual'" x-cloak>
        <x-field :label="__('Video call link (Meet, Jitsi, Zoom…)')" name="video_link"
                 type="url" :value="$service?->video_link" placeholder="https://meet.jit.si/mi-sala" />
        <p class="mt-1 text-xs text-muted">{{ __('It will be included in the client\'s confirmation and reminders.') }}</p>
    </div>

    <details class="rounded-lg border border-line p-4" @if ($errors->hasAny(['buffer_minutes', 'min_notice_hours', 'cancellation_hours', 'max_advance_days'])) open @endif>
        <summary class="cursor-pointer text-sm font-medium">{{ __('Booking rules') }}</summary>
        <div class="mt-4 grid grid-cols-2 gap-4">
            <x-field :label="__('Buffer between appointments (min)')" name="buffer_minutes" type="number"
                     :value="$service?->buffer_minutes ?? 0" required min="0" max="240" step="5" />
            <x-field :label="__('Minimum notice (hours)')" name="min_notice_hours" type="number"
                     :value="$service?->min_notice_hours ?? 2" required min="0" max="168" />
            <x-field :label="__('Cancellation window (hours before)')" name="cancellation_hours" type="number"
                     :value="$service?->cancellation_hours ?? 12" required min="0" max="168" />
            <x-field :label="__('Bookable up to (days ahead)')" name="max_advance_days" type="number"
                     :value="$service?->max_advance_days ?? 60" required min="1" max="365" />
        </div>
    </details>

    <label class="flex items-center gap-2 text-sm">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service?->is_active ?? true))
               class="rounded border-control text-brand-600 focus:ring-brand-500">
        {{ __('Active service (visible for booking)') }}
    </label>

    <x-button>{{ $service ? __('Save changes') : __('Create service') }}</x-button>
</div>
