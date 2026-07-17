<x-app-layout>
    <x-slot:title>{{ __('Nuevo servicio') }}</x-slot:title>

    <h1 class="mb-6 text-2xl font-bold">{{ __('Nuevo servicio') }}</h1>

    <form method="POST" action="{{ route('services.store') }}" class="max-w-lg">
        @csrf
        @include('app.services.form')
    </form>
</x-app-layout>
