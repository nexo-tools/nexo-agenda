<x-guest-layout :title="__('Create your account')">
    <h1 class="mb-1 text-xl font-semibold">{{ __('Create your account') }}</h1>
    <p class="mb-6 text-sm text-muted">{{ __('Your business taking bookings in minutes.') }}</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <x-field :label="__('Your name')" name="name" required autocomplete="name" />
        <x-field :label="__('Email')" name="email" type="email" required autocomplete="username" />
        <x-field :label="__('Password')" name="password" type="password" required autocomplete="new-password" />
        <x-field :label="__('Confirm password')" name="password_confirmation" type="password" required autocomplete="new-password" />

        <hr class="border-line">

        <x-field :label="__('Business name')" name="business_name" required />
        <x-select :label="__('Category')" name="category"
                  :options="collect(config('nexo.categories'))->mapWithKeys(fn ($c) => [$c => __('nexo.categories.'.$c)])" />
        <x-field :label="__('City')" name="city" required />
        <x-field :label="__('WhatsApp (optional)')" name="whatsapp_phone" type="tel" autocomplete="tel" />

        <x-button>{{ __('Create account') }}</x-button>
    </form>

    <p class="mt-4 text-center text-sm text-muted">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="font-medium text-brand-700 hover:underline dark:text-brand-400">{{ __('Sign in') }}</a>
    </p>
</x-guest-layout>
