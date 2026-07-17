@component('emails.layout', ['businessName' => $booking->business->name])
    <h1 style="font-size:20px;margin:0 0 8px;">{{ __('Turno reprogramado') }}</h1>
    <p style="margin:0;">{{ __('Hola :name, tu turno tiene nueva fecha y hora.', ['name' => $booking->client_name]) }}</p>

    @include('emails.partials.booking-details')

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        {{ __('El enlace de gestión de tu email de confirmación sigue siendo válido. Adjuntamos el evento actualizado para tu calendario.') }}
    </p>
@endcomponent
