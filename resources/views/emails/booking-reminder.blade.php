@component('emails.layout', ['businessName' => $booking->business->name])
    <h1 style="font-size:20px;margin:0 0 8px;">{{ __('See you tomorrow') }}</h1>
    <p style="margin:0;">{{ __('Hi :name, here\'s a reminder of your appointment.', ['name' => $booking->client_name]) }}</p>

    @include('emails.partials.booking-details')

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        {{ __('Need to reschedule or cancel? Use the link in your confirmation email.') }}
        @if ($booking->business->whatsapp_phone)
            {{ __('You can also message the business on WhatsApp:') }}
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $booking->business->whatsapp_phone) }}" style="color:#0f766e;">wa.me/{{ preg_replace('/\D/', '', $booking->business->whatsapp_phone) }}</a>
        @endif
    </p>
@endcomponent
