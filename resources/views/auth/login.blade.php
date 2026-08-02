<x-guest-layout :title="__('Sign in')">
    <h1 class="mb-6 text-xl font-semibold">{{ __('Sign in to your account') }}</h1>

    @if (session('status'))
        <p class="nexo-flash mb-4" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <x-field :label="__('Email')" name="email" type="email" required autocomplete="username" />
        <x-field :label="__('Password')" name="password" type="password" required autocomplete="current-password" />

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" class="rounded border-control text-brand-600 focus:ring-brand-500">
            {{ __('Remember me') }}
        </label>

        <x-button>{{ __('Sign in') }}</x-button>
    </form>

    <p class="mt-4 text-center text-sm">
        <a href="{{ route('password.request') }}" class="text-brand-700 hover:underline dark:text-brand-400">
            {{ __('Forgot your password?') }}
        </a>
    </p>

    @if (config('nexo-sso.enabled'))
        <div class="my-4 flex items-center gap-3 text-xs uppercase text-muted">
            <span class="h-px flex-grow bg-line"></span>
            {{ __('Or') }}
            <span class="h-px flex-grow bg-line"></span>
        </div>

        @error('nexo_sso')
            <p class="nexo-flash nexo-flash--danger mb-3" role="alert">{{ $message }}</p>
        @enderror

        <a href="{{ route('nexo-sso.redirect') }}" class="nexo-btn nexo-btn--ghost w-full">
            {{ __('Continue with Nexo ID') }}
        </a>
    @endif

    <p class="mt-4 text-center text-sm text-muted">
        {{ __('Don\'t have an account?') }}
        <a href="{{ route('register') }}" class="font-medium text-brand-700 hover:underline dark:text-brand-400">{{ __('Create account') }}</a>
    </p>
</x-guest-layout>
