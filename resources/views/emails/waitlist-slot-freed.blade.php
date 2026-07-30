@php($local = $booking->starts_at->setTimezone($booking->business->timezone))

@component('emails.layout', ['businessName' => $booking->business->name])
    <h1 style="font-size:20px;margin:0 0 8px;">{{ __('A time slot just opened!') }}</h1>
    <p style="margin:0;">
        {{ __('Hi :name, you were on the waitlist for :service on :date and a spot just opened up (:time).', [
            'name' => $entry->client_name,
            'service' => $booking->service->name,
            'date' => $local->isoFormat('dddd D [de] MMMM'),
            'time' => $local->format('H:i'),
        ]) }}
    </p>

    <a href="{{ route('public.times', [$booking->business, $booking->service, 'professional' => $entry->professional_id ?? 'any', 'date' => $local->toDateString()]) }}"
       style="display:inline-block;background:#0f766e;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:10px;margin-top:16px;">
        {{ __('Book now') }}
    </a>

    <p style="font-size:13px;color:#64748b;margin-top:16px;">
        {{ __('Spots are first come, first served — if the time is gone, pick another from the same link.') }}
    </p>
@endcomponent
