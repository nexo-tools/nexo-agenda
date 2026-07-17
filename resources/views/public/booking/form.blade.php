<x-public-layout :title="__('Tus datos').' — '.$business->name" :business="$business">
    <a href="{{ route('public.times', [$business, $service, 'professional' => $professional->id, 'date' => $start->toDateString()]) }}"
       class="text-sm text-brand-700 hover:underline dark:text-brand-400">
        ← {{ __('Cambiar horario') }}
    </a>
    <p class="mt-3 text-sm text-slate-500">{{ __('Paso 4 de 4') }}</p>
    <h1 class="mb-4 text-xl font-bold">{{ __('Tus datos') }}</h1>

    <div class="mb-5 rounded-2xl bg-brand-50 p-4 text-sm dark:bg-slate-800">
        <p class="font-semibold">{{ $service->name }}</p>
        <p class="capitalize text-slate-700 dark:text-slate-300">
            {{ $start->isoFormat('dddd D [de] MMMM') }} · {{ $start->format('H:i') }} · {{ $professional->name }}
        </p>
        @if ($service->price !== null)
            <p class="text-slate-700 dark:text-slate-300">${{ number_format((float) $service->price, 0, ',', '.') }}</p>
        @endif
    </div>

    <form method="POST" action="{{ route('public.store', [$business, $service]) }}" class="space-y-4">
        @csrf
        <input type="hidden" name="professional_id" value="{{ $professional->id }}">
        <input type="hidden" name="start" value="{{ $start->format('Y-m-d H:i') }}">

        <x-field :label="__('Nombre')" name="client_name" required autocomplete="name" />
        <x-field :label="__('Email')" name="client_email" type="email" required autocomplete="email" />
        <x-field :label="__('Teléfono (opcional)')" name="client_phone" type="tel" autocomplete="tel" />
        <x-field :label="__('Nota para el negocio (opcional)')" name="note" />

        <p class="text-xs text-slate-500">
            {{ __('Sin cuenta ni contraseña: te enviaremos un enlace para ver, reprogramar o cancelar tu turno.') }}
        </p>

        <x-button>{{ __('Confirmar turno') }}</x-button>
    </form>
</x-public-layout>
