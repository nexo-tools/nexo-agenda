<x-public-layout :title="$business->name" :business="$business">
    <x-slot:meta>
        <meta name="description" content="{{ __('Reserva tu turno en :name', ['name' => $business->name]) }}">
    </x-slot:meta>

    <header class="mb-6">
        @if ($business->logo_path)
            <img src="{{ Storage::url($business->logo_path) }}" alt="" class="mb-3 h-16 w-16 rounded-2xl object-contain">
        @endif
        <h1 class="text-2xl font-bold">{{ $business->name }}</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ __('nexo.categories.'.$business->category) }} · {{ $business->city }}
        </p>
        @if ($business->description)
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">{{ $business->description }}</p>
        @endif
    </header>

    <h2 class="mb-3 font-semibold">{{ __('Reserva tu turno') }}</h2>

    @if ($services->isEmpty())
        <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-700">
            {{ __('Este negocio todavía no tiene servicios disponibles para reservar.') }}
        </p>
    @else
        <ul class="space-y-3">
            @foreach ($services as $service)
                <li class="rounded-2xl bg-white p-4 shadow-sm dark:bg-slate-800">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold">
                                {{ $service->name }}
                                @if ($service->mode === \App\Enums\ServiceMode::Virtual)
                                    <span class="ml-1 rounded bg-brand-100 px-2 py-0.5 text-xs text-brand-900 dark:bg-brand-900 dark:text-brand-100">{{ __('Virtual') }}</span>
                                @endif
                            </p>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                {{ $service->duration_minutes }} min
                                @if ($service->price !== null)
                                    · ${{ number_format((float) $service->price, 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('public.professional', [$business, $service]) }}"
                           class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                            {{ __('Reservar') }}
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <div class="mt-8 space-y-1 text-sm text-slate-600 dark:text-slate-400">
        @if ($business->address)
            <p>⌂ {{ $business->address }}</p>
        @endif
        @if ($business->whatsapp_phone)
            <p>
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $business->whatsapp_phone) }}"
                   class="text-brand-700 hover:underline dark:text-brand-400" rel="noopener">
                    ✆ WhatsApp
                </a>
            </p>
        @endif
    </div>
</x-public-layout>
