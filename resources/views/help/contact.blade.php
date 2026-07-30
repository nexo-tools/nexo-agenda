<x-public-layout :title="__('Contact')">
    <header class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('Write to us') }}</h1>
        <p class="mt-1 text-sm text-muted">
            {{ __('Report a problem, suggest an idea, or ask us anything.') }}
        </p>
    </header>

    @if (session('status'))
        <p class="nexo-flash mb-4" role="status">{{ session('status') }}</p>
    @endif

    @php
        $labels = [
            'bug' => __('Report a problem'),
            'idea' => __('Suggest an idea'),
            'negocio' => __('Business inquiry'),
            'otro' => __('Other'),
        ];
    @endphp

    <form method="POST" action="{{ route('contact.store') }}" class="space-y-4 rounded-2xl bg-surface-raised p-5 shadow-sm">
        @csrf
        <input type="hidden" name="page_url" value="{{ url()->previous() }}">

        <x-select :label="__('Type')" name="type"
                  :options="collect($types)->mapWithKeys(fn ($t) => [$t => $labels[$t]])"
                  :selected="old('type', 'bug')" />

        <div>
            <label for="message" class="mb-1 block text-sm font-medium">{{ __('Message') }}</label>
            <textarea id="message" name="message" rows="5" required maxlength="2000"
                      @error('message') aria-invalid="true" aria-describedby="message-error" @enderror
                      class="w-full rounded-lg border-control bg-surface text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('message') }}</textarea>
            @error('message')
                <p id="message-error" class="mt-1 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <x-field :label="__('Email (optional)')" name="email" type="email" autocomplete="email" />
            <p class="mt-1 text-xs text-muted">{{ __('Leave it if you\'d like us to reply.') }}</p>
        </div>

        <x-button>{{ __('Send') }}</x-button>
    </form>
</x-public-layout>
