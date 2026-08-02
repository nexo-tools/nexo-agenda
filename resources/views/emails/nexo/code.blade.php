{{--
    A code, token or URL the person has to read or copy by hand. Monospaced,
    boxed, and breakable: a long signed URL that does not wrap pushes the whole
    email sideways in most clients.

        <x-nexo-mail::code>{{ $url }}</x-nexo-mail::code>
--}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:4px 0 20px;">
    <tr>
        <td class="nexo-panel nexo-ink" style="padding:12px 14px; background-color:#fafafa; border-radius:8px; font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; font-size:13px; line-height:1.5; color:#18181b; word-break:break-all;">
            {{ $slot }}
        </td>
    </tr>
</table>
