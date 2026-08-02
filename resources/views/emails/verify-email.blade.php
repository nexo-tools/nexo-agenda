<x-nexo-mail::layout :title="__('Verify your email')" :preheader="__('Confirm your address to finish setting up your account.')">
    <h1 class="nexo-ink" style="margin:0 0 16px; font-size:20px; line-height:1.3; font-weight:700; color:#18181b;">
        {{ __('Verify your email') }}
    </h1>

    <p style="margin:0 0 20px; font-size:15px; line-height:1.6;">
        {{ __('Confirm your address to finish setting up your account. It is also how you get back in if you forget your password.') }}
    </p>

    <x-nexo-mail::button :url="$url">{{ __('Verify my email') }}</x-nexo-mail::button>

    <p class="nexo-muted" style="margin:16px 0 4px; font-size:13px; line-height:1.6; color:#71717a;">
        {{ __('If the button does not work, copy and paste this link:') }}
    </p>
    <x-nexo-mail::code>{{ $url }}</x-nexo-mail::code>

    <p class="nexo-muted nexo-rule" style="margin:24px 0 0; padding-top:16px; border-top:1px solid #e4e4e7; font-size:12px; line-height:1.6; color:#a1a1aa;">
        {{ __('If you did not create an account, you can ignore this email.') }}
    </p>
</x-nexo-mail::layout>
