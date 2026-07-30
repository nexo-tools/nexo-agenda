<x-guest-layout :title="__('New password')">
    <h1 class="mb-6 text-xl font-bold">{{ __('New password') }}</h1>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-field :label="__('Email')" name="email" type="email" :value="$request->email" required autocomplete="username" />
        <x-field :label="__('Password')" name="password" type="password" required autocomplete="new-password" />
        <x-field :label="__('Confirm password')" name="password_confirmation" type="password" required autocomplete="new-password" />

        <x-button>{{ __('Save password') }}</x-button>
    </form>
</x-guest-layout>
