<x-guest-layout>
    <h1 class="mb-1 text-xl font-bold">{{ __('Restablecer contraseña') }}</h1>
    <p class="mb-6 text-sm text-slate-600 dark:text-slate-400">
        {{ __('Ingresa tu email y te enviaremos un enlace para crear una nueva contraseña.') }}
    </p>

    @if (session('status'))
        <p class="mb-4 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <x-field :label="__('Email')" name="email" type="email" required autocomplete="username" />
        <x-button>{{ __('Enviar enlace') }}</x-button>
    </form>
</x-guest-layout>
