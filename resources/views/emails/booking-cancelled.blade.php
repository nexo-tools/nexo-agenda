@component('emails.layout', ['businessName' => $booking->business->name])
    <h1 style="font-size:20px;margin:0 0 8px;">{{ __('Appointment cancelled') }}</h1>
    <p style="margin:0;">{{ __('Hi :name, your appointment was cancelled.', ['name' => $booking->client_name]) }}</p>

    @include('emails.partials.booking-details')

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        {{ __('You can book a new appointment whenever you need:') }}
        <a href="{{ route('public.business', $booking->business) }}" style="color:#0f766e;">{{ url('/'.$booking->business->slug) }}</a>
    </p>
@endcomponent
