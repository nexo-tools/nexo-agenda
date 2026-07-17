@component('emails.layout', ['businessName' => $booking->business->name])
    <h1 style="font-size:20px;margin:0 0 8px;">{{ __('Te esperamos mañana') }}</h1>
    <p style="margin:0;">{{ __('Hola :name, te recordamos tu turno.', ['name' => $booking->client_name]) }}</p>

    @include('emails.partials.booking-details')

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        {{ __('¿Necesitas reprogramar o cancelar? Usa el enlace de tu email de confirmación.') }}
        @if ($booking->business->whatsapp_phone)
            {{ __('También puedes escribir al negocio por WhatsApp:') }}
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $booking->business->whatsapp_phone) }}" style="color:#0f766e;">wa.me/{{ preg_replace('/\D/', '', $booking->business->whatsapp_phone) }}</a>
        @endif
    </p>
@endcomponent
