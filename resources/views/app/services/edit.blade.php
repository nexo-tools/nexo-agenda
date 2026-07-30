<x-app-layout>
    <x-slot:title>{{ __('Edit service') }}</x-slot:title>

    <h1 class="mb-6 text-2xl font-bold">{{ __('Edit service') }}</h1>

    <form method="POST" action="{{ route('services.update', $service) }}" class="max-w-lg">
        @csrf
        @method('PUT')
        @include('app.services.form')
    </form>
</x-app-layout>
