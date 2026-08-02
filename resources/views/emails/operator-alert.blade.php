{{-- Operator-facing (not translated). Family layout because an operator reading
     this on a phone at night deserves the same shell as anybody else. --}}
<x-nexo-mail::layout title="Something broke" :preheader="$exceptionClass.' — '.$summary">
    <h1 class="nexo-ink" style="margin:0 0 16px; font-size:20px; line-height:1.3; font-weight:700; color:#18181b;">
        Something broke
    </h1>

    <x-nexo-mail::panel :rows="[
        'Exception' => $exceptionClass,
        'Where' => $file.':'.$line,
        'URL' => $url,
    ]" />

    <p style="margin:0 0 4px; font-size:14px; line-height:1.6;"><strong>Message</strong></p>
    <p class="nexo-panel nexo-ink" style="margin:0 0 20px; padding:12px 14px; background-color:#fafafa; border-radius:8px; font-size:14px; line-height:1.6; white-space:pre-line; color:#18181b;">{{ $summary }}</p>

    <p style="margin:0 0 4px; font-size:14px; line-height:1.6;"><strong>First frames</strong></p>
    <x-nexo-mail::code>{{ $trace }}</x-nexo-mail::code>

    <p class="nexo-muted nexo-rule" style="margin:20px 0 0; padding-top:16px; border-top:1px solid #e4e4e7; font-size:12px; line-height:1.6; color:#a1a1aa;">
        One mail per distinct exception every 15 minutes — a loop cannot flood the inbox.
        Turn these off with NEXO_OPS_MAIL=false.
    </p>
</x-nexo-mail::layout>
