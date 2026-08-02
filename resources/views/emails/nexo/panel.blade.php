{{--
    Data panel: the facts of the thing that just happened (a booking, an event,
    a ticket), as label → value rows. Two columns on a desktop client, and the
    value wraps under its label on a phone because the label column is narrow
    and fixed rather than percentage-based.

        <x-nexo-mail::panel :rows="[
            __(…) => $event->title,
            __(…) => $event->starts_at,
        ]" />
--}}
@props(['rows' => []])

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="nexo-panel" style="background-color:#fafafa; border-radius:8px; margin:8px 0 20px;">
    @foreach ($rows as $label => $value)
        @continue($value === null || $value === '')
        <tr>
            <td class="nexo-muted" style="padding:{{ $loop->first ? '14px' : '0' }} 8px 4px 16px; font-size:12px; line-height:1.4; color:#71717a; text-transform:uppercase; letter-spacing:0.04em;">
                {{ $label }}
            </td>
        </tr>
        <tr>
            <td class="nexo-ink" style="padding:0 16px {{ $loop->last ? '14px' : '12px' }}; font-size:15px; line-height:1.5; color:#18181b; font-weight:600;">
                {{ $value }}
            </td>
        </tr>
    @endforeach
</table>
