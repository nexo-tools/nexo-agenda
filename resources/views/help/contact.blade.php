<x-public-layout :title="__('Contacto')">
    <header class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('Escríbenos') }}</h1>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            {{ __('Reporta un problema, propón una idea o consúltanos lo que necesites.') }}
        </p>
    </header>

    @if (session('status'))
        <p class="mb-4 rounded-lg bg-brand-100 px-4 py-3 text-sm text-brand-900" role="status">{{ session('status') }}</p>
    @endif

    @php
        $labels = [
            'bug' => __('Reportar un problema'),
            'idea' => __('Proponer una idea'),
            'negocio' => __('Consulta de negocio'),
            'otro' => __('Otro'),
        ];
    @endphp

    <form method="POST" action="{{ route('contact.store') }}" class="space-y-4 rounded-2xl bg-white p-5 shadow-sm dark:bg-slate-800">
        @csrf
        <input type="hidden" name="page_url" value="{{ url()->previous() }}">

        <x-select :label="__('Tipo')" name="type"
                  :options="collect($types)->mapWithKeys(fn ($t) => [$t => $labels[$t]])"
                  :selected="old('type', 'bug')" />

        <div>
            <label for="message" class="mb-1 block text-sm font-medium">{{ __('Mensaje') }}</label>
            <textarea id="message" name="message" rows="5" required maxlength="2000"
                      @error('message') aria-invalid="true" aria-describedby="message-error" @enderror
                      class="w-full rounded-lg border-slate-300 bg-white text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">{{ old('message') }}</textarea>
            @error('message')
                <p id="message-error" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <x-field :label="__('Email (opcional)')" name="email" type="email" autocomplete="email" />
            <p class="mt-1 text-xs text-slate-500">{{ __('Déjalo si quieres que te respondamos.') }}</p>
        </div>

        <x-button>{{ __('Enviar') }}</x-button>
    </form>
</x-public-layout>
