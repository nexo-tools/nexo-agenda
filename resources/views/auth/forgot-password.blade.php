<x-guest-layout :title="__('Restablecer contraseña')">
    <h1 class="mb-1 text-xl font-bold">{{ __('Restablecer contraseña') }}</h1>
    <p class="mb-6 text-sm text-muted">
        {{ __('Ingresa tu email y te enviaremos un enlace para crear una nueva contraseña.') }}
    </p>

    @if (session('status'))
        <p class="nexo-flash mb-4" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <x-field :label="__('Email')" name="email" type="email" required autocomplete="username" />
        <x-button>{{ __('Enviar enlace') }}</x-button>
    </form>
</x-guest-layout>
