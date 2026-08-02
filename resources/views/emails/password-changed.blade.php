{{-- Security notice. No button to "manage your account": the only action worth
     offering here is the one that helps if this was not you. --}}
<x-nexo-mail::layout :title="__('Your password changed')" :preheader="__('Your password changed')">
    <h1 class="nexo-ink" style="margin:0 0 16px; font-size:20px; line-height:1.3; font-weight:700; color:#18181b;">
        {{ __('Your password changed') }}
    </h1>

    <p style="margin:0 0 20px; font-size:15px; line-height:1.6;">
        {{ __('The password of your account was just changed. If it was you, there is nothing to do.') }}
    </p>

    <p style="margin:0 0 4px; font-size:15px; line-height:1.6;">
        {{ __('If it was not you, reset it right now — whoever did it is signed in:') }}
    </p>
    <x-nexo-mail::code>{{ $resetUrl }}</x-nexo-mail::code>
</x-nexo-mail::layout>
