{{-- The receipt for joining a waitlist: no button, because there is nothing to
     do but wait, and promising an action here would be a lie. --}}
@php($local = \Illuminate\Support\Carbon::parse($entry->date, $entry->business->timezone))

<x-nexo-mail::layout
    :title="__('You are on the waitlist at :business', ['business' => $entry->business->name])"
    :brand="$entry->business->name"
    :preheader="__('We will email you if a time frees up.')">

    <h1 class="nexo-ink" style="margin:0 0 8px; font-size:20px; line-height:1.3; font-weight:700; color:#18181b;">
        {{ __('You are on the waitlist') }}
    </h1>

    <p style="margin:0 0 4px; font-size:15px; line-height:1.6;">
        {{ __('Hi :name, we noted you down. If a time frees up that day, we email you — first come, first served.', ['name' => $entry->client_name]) }}
    </p>

    <x-nexo-mail::panel :rows="[
        __('Service') => $entry->service->name,
        __('When') => ucfirst($local->translatedFormat(__('app.date'))),
        __('With') => $entry->professional?->name,
    ]" />

    <p class="nexo-muted" style="margin:16px 0 0; font-size:13px; line-height:1.6; color:#71717a;">
        {{ __('Being on the list does not hold a spot: it is a heads-up, not a booking.') }}
    </p>
</x-nexo-mail::layout>
