<x-public-layout :title="__('Your details').' — '.$business->name" :business="$business">
    <a href="{{ route('public.times', [$business, $service, 'professional' => $professional->id, 'date' => $start->toDateString()]) }}"
       class="text-sm text-brand-700 hover:underline dark:text-brand-400">
        ← {{ __('Change time') }}
    </a>
    <p class="mt-3 text-sm text-muted">{{ __('Step 4 of 4') }}</p>
    <h1 class="mb-4 text-xl font-bold">{{ __('Your details') }}</h1>

    <div class="mb-5 rounded-2xl bg-bg-subtle p-4 text-sm">
        <p class="font-semibold">{{ $service->name }}</p>
        <p class="capitalize text-ink">
            {{ $start->isoFormat('dddd D [de] MMMM') }} · {{ $start->format('H:i') }} · {{ $professional->name }}
        </p>
        @if ($service->price !== null)
            <p class="text-ink">${{ number_format((float) $service->price, 0, ',', '.') }}</p>
        @endif
    </div>

    <form method="POST" action="{{ route('public.store', [$business, $service]) }}" class="space-y-4">
        @csrf
        <input type="hidden" name="professional_id" value="{{ $professional->id }}">
        <input type="hidden" name="start" value="{{ $start->format('Y-m-d H:i') }}">

        <x-field :label="__('Name')" name="client_name" required autocomplete="name" />
        <x-field :label="__('Email')" name="client_email" type="email" required autocomplete="email" />
        <x-field :label="__('Phone (optional)')" name="client_phone" type="tel" autocomplete="tel" />
        <x-field :label="__('Note for the business (optional)')" name="note" />

        <p class="text-xs text-muted">
            {{ __('No account, no password: we\'ll email you a link to view, reschedule or cancel your appointment.') }}
        </p>

        <x-button>{{ __('Confirm appointment') }}</x-button>
    </form>
</x-public-layout>
