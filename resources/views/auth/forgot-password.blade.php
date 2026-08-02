<x-guest-layout :title="__('Reset password')">
    <h1 class="mb-1 text-xl font-semibold">{{ __('Reset password') }}</h1>
    <p class="mb-6 text-sm text-muted">
        {{ __('Enter your email and we\'ll send you a link to create a new password.') }}
    </p>

    @if (session('status'))
        <p class="nexo-flash mb-4" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <x-field :label="__('Email')" name="email" type="email" required autocomplete="username" />
        <x-button>{{ __('Send link') }}</x-button>
    </form>
</x-guest-layout>
