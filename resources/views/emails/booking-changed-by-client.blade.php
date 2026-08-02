{{-- To the owner. Their side of the management link, which until now only ever
     told the client. --}}
<x-nexo-mail::layout
    :title="$cancelled ? __('Cancelled by the client: :client', ['client' => $booking->client_name]) : __('Rescheduled by the client: :client', ['client' => $booking->client_name])"
    :brand="$booking->business->name"
    :preheader="$cancelled ? __('A client cancelled their appointment.') : __('A client moved their appointment.')">

    <h1 class="nexo-ink" style="margin:0 0 8px; font-size:20px; line-height:1.3; font-weight:700; color:#18181b;">
        {{ $cancelled ? __('A client cancelled') : __('A client rescheduled') }}
    </h1>

    <p style="margin:0 0 4px; font-size:15px; line-height:1.6;">
        {{ $cancelled
            ? __(':client cancelled from their management link. The slot is free again.', ['client' => $booking->client_name])
            : __(':client moved their appointment from their management link. Here is the new time.', ['client' => $booking->client_name]) }}
    </p>

    @include('emails.partials.booking-details')

    @if ($booking->client_phone)
        <p class="nexo-muted" style="margin:0 0 16px; font-size:14px; line-height:1.6; color:#71717a;">
            {{ $booking->client_phone }}@if ($booking->client_email) · {{ $booking->client_email }}@endif
        </p>
    @endif

    <x-nexo-mail::button :url="route('dashboard', ['date' => $booking->starts_at->setTimezone($booking->business->timezone)->toDateString()])">
        {{ __('View in my schedule') }}
    </x-nexo-mail::button>
</x-nexo-mail::layout>
