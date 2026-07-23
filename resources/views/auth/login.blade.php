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

    @if (config('nexo-sso.enabled'))
        <div class="my-4 flex items-center gap-3 text-xs uppercase text-slate-400">
            <span class="h-px flex-grow bg-slate-200 dark:bg-slate-700"></span>
            {{ __('o') }}
            <span class="h-px flex-grow bg-slate-200 dark:bg-slate-700"></span>
        </div>

        @error('nexo_sso')
            <p class="mb-3 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300" role="alert">{{ $message }}</p>
        @enderror

        <a href="{{ route('nexo-sso.redirect') }}"
           class="flex w-full items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
            {{ __('Continuar con Nexo ID') }}
        </a>
    @endif

    <p class="mt-4 text-center text-sm text-slate-600 dark:text-slate-400">
        {{ __('¿No tienes cuenta?') }}
        <a href="{{ route('register') }}" class="font-medium text-brand-700 hover:underline dark:text-brand-400">{{ __('Regístrate') }}</a>
    </p>
</x-guest-layout>
