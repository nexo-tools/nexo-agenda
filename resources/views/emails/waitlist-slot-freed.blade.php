@php($local = $booking->starts_at->setTimezone($booking->business->timezone))

<x-nexo-mail::layout
    :title="__('A time slot just opened at :business!', ['business' => $booking->business->name])"
    :brand="$booking->business->name"
    :preheader="__('A spot just opened for :service.', ['service' => $booking->service->name])">

    <h1 class="nexo-ink" style="margin:0 0 8px; font-size:20px; line-height:1.3; font-weight:700; color:#18181b;">
        {{ __('A time slot just opened!') }}
    </h1>
    <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
        {{ __('Hi :name, you were on the waitlist for :service on :date and a spot just opened up (:time).', [
            'name' => $entry->client_name,
            'service' => $booking->service->name,
            'date' => ucfirst($local->translatedFormat(__('app.datetime'))),
            'time' => $local->format('H:i'),
        ]) }}
    </p>

    <x-nexo-mail::button :url="route('public.times', [$booking->business, $booking->service, 'professional' => $entry->professional_id ?? 'any', 'date' => $local->toDateString()])">
        {{ __('Book now') }}
    </x-nexo-mail::button>

    <p class="nexo-muted" style="margin:16px 0 0; font-size:13px; line-height:1.6; color:#71717a;">
        {{ __('Spots are first come, first served — if the time is gone, pick another from the same link.') }}
    </p>
</x-nexo-mail::layout>
