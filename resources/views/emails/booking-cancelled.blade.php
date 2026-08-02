<x-nexo-mail::layout
    :title="__('Your appointment at :business was cancelled', ['business' => $booking->business->name])"
    :brand="$booking->business->name"
    :preheader="__('Hi :name, your appointment was cancelled.', ['name' => $booking->client_name])">

    <h1 class="nexo-ink" style="margin:0 0 8px; font-size:20px; line-height:1.3; font-weight:700; color:#18181b;">
        {{ __('Appointment cancelled') }}
    </h1>
    <p style="margin:0 0 4px; font-size:15px; line-height:1.6;">
        {{ __('Hi :name, your appointment was cancelled.', ['name' => $booking->client_name]) }}
    </p>

    @include('emails.partials.booking-details')

    <p class="nexo-muted" style="margin:16px 0 4px; font-size:13px; line-height:1.6; color:#71717a;">
        {{ __('You can book a new appointment whenever you need:') }}
    </p>
    <x-nexo-mail::code>{{ route('public.business', $booking->business) }}</x-nexo-mail::code>
</x-nexo-mail::layout>
