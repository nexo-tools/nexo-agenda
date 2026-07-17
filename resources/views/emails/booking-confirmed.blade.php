@component('emails.layout', ['businessName' => $booking->business->name])
    <h1 style="font-size:20px;margin:0 0 8px;">{{ __('¡Turno confirmado!') }}</h1>
    <p style="margin:0;">{{ __('Hola :name, tu reserva quedó confirmada.', ['name' => $booking->client_name]) }}</p>

    @include('emails.partials.booking-details')

    <a href="{{ route('booking.manage', $managementToken) }}"
       style="display:inline-block;background:#0f766e;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:10px;">
        {{ __('Ver o gestionar mi turno') }}
    </a>

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        {{ __('Guarda este email: desde el botón puedes reprogramar o cancelar (hasta :hours h antes). Adjuntamos el evento para tu calendario.', ['hours' => $booking->service->cancellation_hours]) }}
    </p>
@endcomponent
