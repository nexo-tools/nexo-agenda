<x-guest-layout :title="__('Nueva contraseña')">
    <h1 class="mb-6 text-xl font-bold">{{ __('Nueva contraseña') }}</h1>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-field :label="__('Email')" name="email" type="email" :value="$request->email" required autocomplete="username" />
        <x-field :label="__('Contraseña')" name="password" type="password" required autocomplete="new-password" />
        <x-field :label="__('Confirmar contraseña')" name="password_confirmation" type="password" required autocomplete="new-password" />

        <x-button>{{ __('Guardar contraseña') }}</x-button>
    </form>
</x-guest-layout>
