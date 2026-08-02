<x-nexo-mail::layout
    :title="__('Reminder: your appointment at :business', ['business' => $booking->business->name])"
    :brand="$booking->business->name"
    :preheader="__('Hi :name, here\'s a reminder of your appointment.', ['name' => $booking->client_name])">

    <h1 class="nexo-ink" style="margin:0 0 8px; font-size:20px; line-height:1.3; font-weight:700; color:#18181b;">
        {{ __('See you tomorrow') }}
    </h1>
    <p style="margin:0 0 4px; font-size:15px; line-height:1.6;">
        {{ __('Hi :name, here\'s a reminder of your appointment.', ['name' => $booking->client_name]) }}
    </p>

    @include('emails.partials.booking-details')

    <p class="nexo-muted" style="margin:16px 0 0; font-size:13px; line-height:1.6; color:#71717a;">
        {{ __('Need to reschedule or cancel? Use the link in your confirmation email.') }}
        @if ($booking->business->whatsapp_phone)
            {{ __('You can also message the business on WhatsApp:') }}
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $booking->business->whatsapp_phone) }}" class="nexo-accent" style="color:#7c3aed;">wa.me/{{ preg_replace('/\D/', '', $booking->business->whatsapp_phone) }}</a>
        @endif
    </p>
</x-nexo-mail::layout>
