@php($local = $booking->starts_at->setTimezone($booking->business->timezone))

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdfa;border-radius:12px;margin:16px 0;">
    <tr>
        <td style="padding:16px 20px;font-size:14px;color:#0f172a;line-height:1.7;">
            <strong>{{ $booking->service->name }}</strong><br>
            <span style="text-transform:capitalize;">{{ $local->isoFormat('dddd D [de] MMMM YYYY') }}</span> · {{ $local->format('H:i') }}<br>
            {{ __('With :name', ['name' => $booking->professional->name]) }}
            @if ($booking->service->price !== null)
                <br>${{ number_format((float) $booking->service->price, 0, ',', '.') }}
            @endif
            @if ($booking->service->mode === \App\Enums\ServiceMode::Virtual && $booking->service->video_link)
                <br><a href="{{ $booking->service->video_link }}" style="color:#0f766e;">{{ __('Video call link') }}</a>
            @elseif ($booking->business->address)
                <br>{{ $booking->business->address }}, {{ $booking->business->city }}
            @endif
        </td>
    </tr>
</table>
