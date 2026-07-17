@php($local = $booking->starts_at->setTimezone($booking->business->timezone))

<x-public-layout :title="__('Tu turno').' — '.$booking->business->name">
    @if (session('status'))
        <p class="mb-4 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900" role="status">{{ session('status') }}</p>
    @endif

    <div class="rounded-2xl bg-white p-6 shadow-sm dark:bg-slate-800">
        <p class="text-sm text-slate-500">{{ __('Tu turno en') }}</p>
        <h1 class="text-xl font-bold">{{ $booking->business->name }}</h1>

        <dl class="mt-4 space-y-1 text-sm">
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500">{{ __('Servicio') }}</dt>
                <dd class="font-medium">{{ $booking->service->name }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500">{{ __('Fecha') }}</dt>
                <dd class="font-medium capitalize">{{ $local->isoFormat('dddd D [de] MMMM YYYY') }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500">{{ __('Hora') }}</dt>
                <dd class="font-medium">{{ $local->format('H:i') }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500">{{ __('Con') }}</dt>
                <dd class="font-medium">{{ $booking->professional->name }}</dd>
            </div>
            @if ($booking->service->price !== null)
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">{{ __('Precio') }}</dt>
                    <dd class="font-medium">${{ number_format((float) $booking->service->price, 0, ',', '.') }}</dd>
                </div>
            @endif
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500">{{ __('Estado') }}</dt>
                <dd class="font-medium">{{ $booking->status->label() }}</dd>
            </div>
        </dl>

        @if ($booking->status === \App\Enums\BookingStatus::Confirmed && $booking->service->mode === \App\Enums\ServiceMode::Virtual && $booking->service->video_link)
            <a href="{{ $booking->service->video_link }}" rel="noopener"
               class="mt-4 block rounded-lg bg-brand-700 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-brand-800">
                {{ __('Unirse a la videollamada') }}
            </a>
        @endif

        @if ($booking->business->address)
            <p class="mt-4 text-sm text-slate-600 dark:text-slate-400">⌂ {{ $booking->business->address }}</p>
        @endif
    </div>

    <p class="mt-4 text-center text-xs text-slate-500">
        {{ __('Guarda este enlace: es tu comprobante y desde aquí gestionas tu turno.') }}
    </p>
</x-public-layout>
