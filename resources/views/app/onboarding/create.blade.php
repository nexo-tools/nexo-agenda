<x-guest-layout :title="__('Create your business')">
    <h1 class="mb-1 text-xl font-bold">{{ __('Create your business') }}</h1>
    <p class="mb-6 text-sm text-muted">{{ __('One last step: tell us about your business to start taking bookings.') }}</p>

    <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-4">
        @csrf

        <x-field :label="__('Business name')" name="business_name" required />
        <x-select :label="__('Category')" name="category"
                  :options="collect(config('nexo.categories'))->mapWithKeys(fn ($c) => [$c => __('nexo.categories.'.$c)])" />
        <x-field :label="__('City')" name="city" required />
        <x-field :label="__('WhatsApp (optional)')" name="whatsapp_phone" type="tel" autocomplete="tel" />

        <x-button>{{ __('Create business') }}</x-button>
    </form>

    <form method="POST" action="{{ config('nexo-sso.enabled') ? route('nexo-sso.logout') : route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm text-muted hover:underline">{{ __('Log out') }}</button>
    </form>
</x-guest-layout>
