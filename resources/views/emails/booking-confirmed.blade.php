@component('emails.layout', ['businessName' => $booking->business->name])
    <h1 style="font-size:20px;margin:0 0 8px;">{{ __('Appointment confirmed!') }}</h1>
    <p style="margin:0;">{{ __('Hi :name, your booking is confirmed.', ['name' => $booking->client_name]) }}</p>

    @include('emails.partials.booking-details')

    <a href="{{ route('booking.manage', $managementToken) }}"
       style="display:inline-block;background:#0f766e;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:10px;">
        {{ __('View or manage my appointment') }}
    </a>

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        {{ __('Keep this email: the button lets you reschedule or cancel (up to :hours h before). The calendar event is attached.', ['hours' => $booking->service->cancellation_hours]) }}
    </p>
@endcomponent
