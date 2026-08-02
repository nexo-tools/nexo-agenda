<x-nexo-mail::layout
    :title="__('New booking: :client — :service', ['client' => $booking->client_name, 'service' => $booking->service->name])"
    :brand="$booking->business->name"
    :preheader="__(':client booked an appointment.', ['client' => $booking->client_name])">

    <h1 class="nexo-ink" style="margin:0 0 8px; font-size:20px; line-height:1.3; font-weight:700; color:#18181b;">
        {{ __('New booking') }}
    </h1>
    <p style="margin:0 0 4px; font-size:15px; line-height:1.6;">
        {{ __(':client booked an appointment.', ['client' => $booking->client_name]) }}
        @if ($booking->client_phone) · {{ $booking->client_phone }} @endif
        @if ($booking->client_email) · {{ $booking->client_email }} @endif
    </p>

    @include('emails.partials.booking-details')

    @if ($booking->note)
        <p style="margin:0 0 16px; font-size:14px; line-height:1.6;"><strong>{{ __('Note') }}:</strong> {{ $booking->note }}</p>
    @endif

    <x-nexo-mail::button :url="route('dashboard', ['date' => $booking->starts_at->setTimezone($booking->business->timezone)->toDateString()])">
        {{ __('View in my schedule') }}
    </x-nexo-mail::button>
</x-nexo-mail::layout>
