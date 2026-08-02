<x-nexo-mail::layout
    :title="__('Appointment confirmed at :business', ['business' => $booking->business->name])"
    :brand="$booking->business->name"
    :preheader="__('Hi :name, your booking is confirmed.', ['name' => $booking->client_name])">

    <h1 class="nexo-ink" style="margin:0 0 8px; font-size:20px; line-height:1.3; font-weight:700; color:#18181b;">
        {{ __('Appointment confirmed!') }}
    </h1>
    <p style="margin:0 0 4px; font-size:15px; line-height:1.6;">
        {{ __('Hi :name, your booking is confirmed.', ['name' => $booking->client_name]) }}
    </p>

    @include('emails.partials.booking-details')

    <x-nexo-mail::button :url="route('booking.manage', $managementToken)">
        {{ __('View or manage my appointment') }}
    </x-nexo-mail::button>

    <p class="nexo-muted" style="margin:16px 0 0; font-size:13px; line-height:1.6; color:#71717a;">
        {{ __('Keep this email: the button lets you reschedule or cancel (up to :hours h before). The calendar event is attached.', ['hours' => $booking->service->cancellation_hours]) }}
    </p>
</x-nexo-mail::layout>
