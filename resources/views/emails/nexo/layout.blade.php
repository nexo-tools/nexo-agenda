{{--
    Nexo mail layout — the shell every transactional email of the ecosystem
    renders inside. Copy the whole templates/nexo-mail/ directory to
    resources/views/emails/nexo/ and register it once (see README):

        Blade::anonymousComponentPath(resource_path('views/emails/nexo'), 'nexo-mail');

    Then a mail view is:

        <x-nexo-mail::layout :title="__(…)" :preheader="...">
            ...body...
        </x-nexo-mail::layout>

    WHY IT LOOKS LIKE 2004. Mail clients strip <style> blocks (Gmail keeps some,
    Outlook keeps almost none), know nothing about the app's CSS or design
    tokens, and lay out with a Word engine on Windows. So: tables, inline styles,
    literal hex instead of var(--nexo-*), 600px max width, no flexbox, no grid.
    The views under resources/views/emails/ are allow-listed in
    NoHardcodedColorsTest for exactly this reason.

    Dark mode is a best effort: the @media block below is honoured by Apple Mail,
    iOS Mail and (partially) Outlook.com, and ignored by the rest, which see the
    light version. That is the accepted limit of HTML email — the alternative is
    forcing dark on clients that would then render our light images on it.

    The isotype ships as PNG, not the SVG the web chrome uses: Gmail strips SVG
    entirely and Outlook does not render it. Every tool already generates
    apple-touch-icon.png (BrandAssetsPresentTest), so that is the default mark.
--}}
@props([
    // Shown in <title> and used as the preheader when none is given.
    'title' => null,
    // The grey line a phone shows next to the subject. Say something useful:
    // "Your ticket for X" beats the first words of the body.
    'preheader' => null,
    // Tool name and its PNG mark, absolute URLs — a mail client has no origin.
    'brand' => null,
    'mark' => null,
])
@php
    $brand = $brand ?: config('app.name');
    $mark = $mark ?: url('/apple-touch-icon.png');
    $preheader = $preheader ?: $title;
    $attrLabel = config('nexo.attribution.label');
    $attrUrl = config('nexo.attribution.url');
    $helpUrl = \Illuminate\Support\Facades\Route::has('help') ? route('help') : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $title ?: $brand }}</title>
    <style>
        @media (prefers-color-scheme: dark) {
            .nexo-bg { background-color: #09090b !important; }
            .nexo-card { background-color: #18181b !important; border-color: #27272a !important; }
            .nexo-ink { color: #fafafa !important; }
            .nexo-body-text { color: #d4d4d8 !important; }
            .nexo-muted { color: #a1a1aa !important; }
            .nexo-accent { color: #a78bfa !important; }
            .nexo-rule { border-color: #27272a !important; }
            .nexo-panel { background-color: #27272a !important; }
        }
        @media only screen and (max-width: 600px) {
            .nexo-shell { width: 100% !important; }
            .nexo-pad { padding-left: 20px !important; padding-right: 20px !important; }
        }
    </style>
</head>
<body class="nexo-bg" style="margin:0; padding:0; background-color:#f4f4f5; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
<!-- nexo-mail -->
{{-- Preheader: hidden in the body, shown by the client next to the subject. --}}
<div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">{{ $preheader }}</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="nexo-bg" style="background-color:#f4f4f5;">
    <tr>
        <td align="center" style="padding:24px 12px;">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" class="nexo-shell" style="width:600px; max-width:600px;">

                {{-- Header: which tool is writing to you. --}}
                <tr>
                    <td align="left" style="padding:0 8px 16px;">
                        <img src="{{ $mark }}" alt="" width="28" height="28" style="vertical-align:middle; border:0; border-radius:6px;">
                        <span class="nexo-ink" style="vertical-align:middle; padding-left:8px; font-size:15px; font-weight:700; color:#18181b;">{{ $brand }}</span>
                    </td>
                </tr>

                {{-- Body card. --}}
                <tr>
                    <td class="nexo-card" style="background-color:#ffffff; border:1px solid #e4e4e7; border-radius:12px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td class="nexo-pad nexo-body-text" style="padding:32px; font-size:15px; line-height:1.6; color:#3f3f46;">
                                    {{ $slot }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Footer: who sent this, where to get help, and who runs the
                     instance. A self-hosted instance says its own name here. --}}
                <tr>
                    <td class="nexo-muted" style="padding:20px 8px 0; font-size:12px; line-height:1.7; color:#a1a1aa;">
                        <a href="{{ url('/') }}" class="nexo-muted" style="color:#a1a1aa; text-decoration:none;">{{ $brand }}</a>
                        @if ($helpUrl)
                            &middot; <a href="{{ $helpUrl }}" class="nexo-muted" style="color:#a1a1aa; text-decoration:underline;">{{ __('nexo.help.title') }}</a>
                        @endif
                        @if ($attrLabel)
                            <br>
                            @if ($attrUrl)
                                <a href="{{ $attrUrl }}" class="nexo-muted" style="color:#a1a1aa; text-decoration:underline;">{{ $attrLabel }}</a>
                            @else
                                {{ $attrLabel }}
                            @endif
                        @endif
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
