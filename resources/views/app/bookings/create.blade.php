<x-app-layout>
    <x-slot:title>{{ __('New appointment') }}</x-slot:title>

    <a href="{{ route('dashboard') }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">← {{ __('Schedule') }}</a>
    <h1 class="mb-6 mt-2 text-2xl font-bold">{{ __('New appointment') }}</h1>

    @if ($services->isEmpty() || $professionals->isEmpty())
        <p class="rounded-2xl border border-dashed border-line-strong p-6 text-sm text-muted">
            {{ __('You need at least one active service and professional to create appointments.') }}
        </p>
    @else
        <form method="POST" action="{{ route('bookings.store') }}" class="max-w-lg space-y-4">
            @csrf

            <x-select :label="__('Service')" name="service_id"
                      :options="$services->pluck('name', 'id')" />
            <x-select :label="__('Professional')" name="professional_id"
                      :options="$professionals->pluck('name', 'id')" />

            <div class="grid grid-cols-2 gap-4">
                <x-field :label="__('Date')" name="date" type="date" :value="$suggestedDate" required />
                <x-field :label="__('Time')" name="time" type="time" required />
            </div>

            <x-field :label="__('Client name')" name="client_name" required />
            <x-field :label="__('Email (optional)')" name="client_email" type="email" />
            <x-field :label="__('Phone (optional)')" name="client_phone" type="tel" />
            <x-field :label="__('Note (optional)')" name="note" />

            <p class="text-xs text-muted">
                {{ __('Manual appointments skip the notice rules: only conflicts are checked.') }}
            </p>

            <x-button>{{ __('Create appointment') }}</x-button>
        </form>
    @endif
</x-app-layout>
