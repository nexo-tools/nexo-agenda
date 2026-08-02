{{--
    Bulletproof button. A styled <a> is invisible to Outlook's Word engine, so
    the shape is a one-cell table with a real bgcolor; the anchor only carries
    the text. Always follow it with the raw URL as a fallback link (see
    templates/nexo-mail/README.md): clients that block images and proxies that
    rewrite hrefs both eat buttons, and the person still has to get in.

        <x-nexo-mail::button :url="$url">{{ __(…) }}</x-nexo-mail::button>
--}}
@props(['url'])

<table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:8px auto 4px;">
    <tr>
        <td align="center" bgcolor="#7c3aed" style="background-color:#7c3aed; border-radius:8px;">
            <a href="{{ $url }}"
               style="display:inline-block; padding:13px 26px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; font-size:15px; font-weight:600; line-height:1; color:#ffffff; text-decoration:none;">
                {{ $slot }}
            </a>
        </td>
    </tr>
</table>
