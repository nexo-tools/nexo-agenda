<x-app-layout>
    <x-slot:title>{{ __('Nuevo turno') }}</x-slot:title>

    <a href="{{ route('dashboard') }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">← {{ __('Agenda') }}</a>
    <h1 class="mb-6 mt-2 text-2xl font-bold">{{ __('Nuevo turno') }}</h1>

    @if ($services->isEmpty() || $professionals->isEmpty())
        <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700">
            {{ __('Necesitas al menos un servicio y un profesional activos para crear turnos.') }}
        </p>
    @else
        <form method="POST" action="{{ route('bookings.store') }}" class="max-w-lg space-y-4">
            @csrf

            <x-select :label="__('Servicio')" name="service_id"
                      :options="$services->pluck('name', 'id')" />
            <x-select :label="__('Profesional')" name="professional_id"
                      :options="$professionals->pluck('name', 'id')" />

            <div class="grid grid-cols-2 gap-4">
                <x-field :label="__('Fecha')" name="date" type="date" :value="$suggestedDate" required />
                <x-field :label="__('Hora')" name="time" type="time" required />
            </div>

            <x-field :label="__('Nombre del cliente')" name="client_name" required />
            <x-field :label="__('Email (opcional)')" name="client_email" type="email" />
            <x-field :label="__('Teléfono (opcional)')" name="client_phone" type="tel" />
            <x-field :label="__('Nota (opcional)')" name="note" />

            <p class="text-xs text-slate-500">
                {{ __('Los turnos manuales no siguen las reglas de anticipación: solo se valida que el horario esté libre.') }}
            </p>

            <x-button>{{ __('Crear turno') }}</x-button>
        </form>
    @endif
</x-app-layout>
