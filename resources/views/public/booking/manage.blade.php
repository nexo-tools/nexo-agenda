@php($local = $booking->starts_at->setTimezone($booking->business->timezone))

<x-public-layout :title="__('Tu turno').' — '.$booking->business->name" :business="$booking->business">
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

        @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
            <div class="mt-5 border-t border-slate-200 pt-5 text-center dark:border-slate-700">
                <div class="mx-auto inline-block rounded-xl bg-white p-2">
                    {!! app(\App\Services\QrSvg::class)->forUrl(route('checkin', $token), 180) !!}
                </div>
                <p class="mt-2 text-xs text-slate-500">{{ __('Muestra este código al llegar para hacer el check-in.') }}</p>
            </div>
        @endif
    </div>

    @if ($booking->clientCanManage())
        <div class="mt-4 flex gap-3">
            <a href="{{ route('booking.reschedule', $token) }}"
               class="flex-1 rounded-lg border border-brand-700 px-4 py-2 text-center text-sm font-semibold text-brand-700 hover:bg-brand-50 dark:border-brand-400 dark:text-brand-400 dark:hover:bg-slate-800">
                {{ __('Reprogramar') }}
            </a>
            <form method="POST" action="{{ route('booking.cancel', $token) }}" class="flex-1"
                  onsubmit="return confirm(@js(__('¿Cancelar tu turno?')))">
                @csrf
                <button class="w-full rounded-lg border border-red-600 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 dark:border-red-400 dark:text-red-400 dark:hover:bg-slate-800">
                    {{ __('Cancelar turno') }}
                </button>
            </form>
        </div>
        <p class="mt-2 text-center text-xs text-slate-500">
            {{ __('Puedes cancelar o reprogramar hasta :hours h antes.', ['hours' => $booking->service->cancellation_hours]) }}
        </p>
    @elseif ($booking->status === \App\Enums\BookingStatus::Confirmed)
        <p class="mt-4 text-center text-xs text-slate-500">
            {{ __('El plazo para cancelar o reprogramar en línea ya pasó. Contacta al negocio.') }}
        </p>
    @endif

    @if ($booking->canBeReviewed())
        <section class="mt-6 rounded-2xl bg-white p-5 shadow-sm dark:bg-slate-800">
            <h2 class="font-semibold">{{ __('¿Cómo estuvo tu experiencia?') }}</h2>
            <form method="POST" action="{{ route('booking.review', $token) }}" class="mt-3 space-y-3">
                @csrf
                <fieldset>
                    <legend class="sr-only">{{ __('Calificación') }}</legend>
                    <div class="rating gap-1 text-3xl">
                        @foreach ([5, 4, 3, 2, 1] as $stars)
                            <label>
                                <input type="radio" name="rating" value="{{ $stars }}" class="sr-only" @checked(old('rating') == $stars) required>
                                <span aria-hidden="true">★</span>
                                <span class="sr-only">{{ trans_choice(':count estrella|:count estrellas', $stars) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('rating')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div>
                    <label for="comment" class="mb-1 block text-sm font-medium">{{ __('Comentario (opcional)') }}</label>
                    <textarea id="comment" name="comment" rows="3" maxlength="500"
                              class="w-full rounded-lg border-slate-300 bg-white text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">{{ old('comment') }}</textarea>
                    @error('comment')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <x-button>{{ __('Enviar reseña') }}</x-button>
            </form>
        </section>
    @elseif ($booking->review)
        <p class="mt-4 rounded-2xl bg-white p-4 text-center text-sm text-slate-600 shadow-sm dark:bg-slate-800 dark:text-slate-400">
            {{ __('Calificaste esta visita con :rating de 5. ¡Gracias!', ['rating' => $booking->review->rating]) }}
        </p>
    @endif

    <p class="mt-4 text-center text-xs text-slate-500">
        {{ __('Guarda este enlace: es tu comprobante y desde aquí gestionas tu turno.') }}
    </p>
</x-public-layout>
