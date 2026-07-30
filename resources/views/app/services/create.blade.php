<x-app-layout>
    <x-slot:title>{{ __('New service') }}</x-slot:title>

    <h1 class="mb-6 text-2xl font-bold">{{ __('New service') }}</h1>

    <form method="POST" action="{{ route('services.store') }}" class="max-w-lg">
        @csrf
        @include('app.services.form')
    </form>
</x-app-layout>
