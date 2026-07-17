<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:-apple-system,'Segoe UI',Roboto,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:#0f766e;padding:20px 28px;">
                            <span style="color:#ffffff;font-size:18px;font-weight:bold;">{{ $businessName ?? config('app.name') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;color:#0f172a;font-size:15px;line-height:1.6;">
                            {{ $slot }}
                        </td>
                    </tr>
                </table>
                <p style="color:#94a3b8;font-size:12px;margin-top:16px;">
                    @if (config('nexo.attribution_text'))
                        {{ config('nexo.attribution_text') }} ·
                    @endif
                    {{ config('app.name') }}
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
