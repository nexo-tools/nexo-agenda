@component('emails.layout', ['businessName' => $booking->business->name])
    <h1 style="font-size:20px;margin:0 0 8px;">{{ __('New booking') }}</h1>
    <p style="margin:0;">
        {{ __(':client booked an appointment.', ['client' => $booking->client_name]) }}
        @if ($booking->client_phone)
            · {{ $booking->client_phone }}
        @endif
        @if ($booking->client_email)
            · {{ $booking->client_email }}
        @endif
    </p>

    @include('emails.partials.booking-details')

    @if ($booking->note)
        <p style="font-size:14px;"><strong>{{ __('Note') }}:</strong> {{ $booking->note }}</p>
    @endif

    <a href="{{ route('dashboard', ['date' => $booking->starts_at->setTimezone($booking->business->timezone)->toDateString()]) }}"
       style="display:inline-block;background:#0f766e;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:10px;">
        {{ __('View in my schedule') }}
    </a>
@endcomponent
