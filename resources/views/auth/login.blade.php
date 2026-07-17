<x-guest-layout>
    <h1 class="mb-6 text-xl font-bold">{{ __('Inicia sesión') }}</h1>

    @if (session('status'))
        <p class="mb-4 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <x-field :label="__('Email')" name="email" type="email" required autocomplete="username" />
        <x-field :label="__('Contraseña')" name="password" type="password" required autocomplete="current-password" />

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                {{ __('Recordarme') }}
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-brand-700 hover:underline dark:text-brand-400">
                {{ __('¿Olvidaste tu contraseña?') }}
            </a>
        </div>

        <x-button>{{ __('Entrar') }}</x-button>
    </form>

    <p class="mt-4 text-center text-sm text-slate-600 dark:text-slate-400">
        {{ __('¿No tienes cuenta?') }}
        <a href="{{ route('register') }}" class="font-medium text-brand-700 hover:underline dark:text-brand-400">{{ __('Regístrate') }}</a>
    </p>
</x-guest-layout>
