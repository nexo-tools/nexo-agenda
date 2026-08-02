@php
    $local = $booking->starts_at->setTimezone($booking->business->timezone);
    $place = null;

    if ($booking->service->mode === \App\Enums\ServiceMode::Virtual && $booking->service->video_link) {
        $place = $booking->service->video_link;
    } elseif ($booking->business->address) {
        $place = $booking->business->address.', '.$booking->business->city;
    }
@endphp

{{-- translatedFormat, not isoFormat with a literal [de]: that bracket shipped
     the Spanish preposition inside every locale, so an English reader got
     "Monday 4 de August". The pattern itself is a translation key now. --}}
<x-nexo-mail::panel :rows="[
    __('Service') => $booking->service->name,
    __('When') => ucfirst($local->translatedFormat(__('app.datetime'))),
    __('With') => $booking->professional->name,
    __('Price') => $booking->service->price !== null ? '$'.number_format((float) $booking->service->price, 0, ',', '.') : null,
    __('Where') => $place,
]" />
