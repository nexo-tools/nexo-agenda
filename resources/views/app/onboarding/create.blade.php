<x-guest-layout>
    <h1 class="mb-1 text-xl font-bold">{{ __('Crea tu negocio') }}</h1>
    <p class="mb-6 text-sm text-slate-600 dark:text-slate-400">{{ __('Un último paso: cuéntanos de tu negocio para empezar a recibir reservas.') }}</p>

    <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-4">
        @csrf

        <x-field :label="__('Nombre del negocio')" name="business_name" required />
        <x-select :label="__('Rubro')" name="category"
                  :options="collect(config('nexo.categories'))->mapWithKeys(fn ($c) => [$c => __('nexo.categories.'.$c)])" />
        <x-field :label="__('Ciudad')" name="city" required />
        <x-field :label="__('WhatsApp (opcional)')" name="whatsapp_phone" type="tel" autocomplete="tel" />

        <x-button>{{ __('Crear negocio') }}</x-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm text-slate-500 hover:underline dark:text-slate-400">{{ __('Cerrar sesión') }}</button>
    </form>
</x-guest-layout>
