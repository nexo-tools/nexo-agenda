@component('emails.layout', ['businessName' => $booking->business->name])
    <h1 style="font-size:20px;margin:0 0 8px;">{{ __('Appointment rescheduled') }}</h1>
    <p style="margin:0;">{{ __('Hi :name, your appointment has a new date and time.', ['name' => $booking->client_name]) }}</p>

    @include('emails.partials.booking-details')

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        {{ __('The management link in your confirmation email is still valid. The updated calendar event is attached.') }}
    </p>
@endcomponent
