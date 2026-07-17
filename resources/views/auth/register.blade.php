<x-guest-layout>
    <h1 class="mb-1 text-xl font-bold">{{ __('Crea tu cuenta') }}</h1>
    <p class="mb-6 text-sm text-slate-600 dark:text-slate-400">{{ __('Tu negocio recibiendo reservas en minutos.') }}</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <x-field :label="__('Tu nombre')" name="name" required autocomplete="name" />
        <x-field :label="__('Email')" name="email" type="email" required autocomplete="username" />
        <x-field :label="__('Contraseña')" name="password" type="password" required autocomplete="new-password" />
        <x-field :label="__('Confirmar contraseña')" name="password_confirmation" type="password" required autocomplete="new-password" />

        <hr class="border-slate-200 dark:border-slate-700">

        <x-field :label="__('Nombre del negocio')" name="business_name" required />
        <x-select :label="__('Rubro')" name="category"
                  :options="collect(config('nexo.categories'))->mapWithKeys(fn ($c) => [$c => __('nexo.categories.'.$c)])" />
        <x-field :label="__('Ciudad')" name="city" required />
        <x-field :label="__('WhatsApp (opcional)')" name="whatsapp_phone" type="tel" autocomplete="tel" />

        <x-button>{{ __('Crear cuenta') }}</x-button>
    </form>

    <p class="mt-4 text-center text-sm text-slate-600 dark:text-slate-400">
        {{ __('¿Ya tienes cuenta?') }}
        <a href="{{ route('login') }}" class="font-medium text-brand-700 hover:underline dark:text-brand-400">{{ __('Inicia sesión') }}</a>
    </p>
</x-guest-layout>
