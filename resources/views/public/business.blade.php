<x-public-layout :title="$business->name" :business="$business"
    :description="__('Reserva tu turno en :name', ['name' => $business->name])">
    <header class="mb-6">
        @if ($business->logo_path)
            <img src="{{ Storage::url($business->logo_path) }}" alt="" class="mb-3 h-16 w-16 rounded-2xl object-contain">
        @endif
        <h1 class="text-2xl font-bold">{{ $business->name }}</h1>
        <p class="text-sm text-muted">
            {{ __('nexo.categories.'.$business->category) }} · {{ $business->city }}
            @if ($ratingCount > 0)
                · <span aria-hidden="true" class="text-amber-500">★</span>
                {{ number_format($ratingAverage, 1, ',') }}
                <span class="text-muted">({{ $ratingCount }})</span>
            @endif
        </p>
        @if ($business->description)
            <p class="mt-2 text-sm text-ink">{{ $business->description }}</p>
        @endif
    </header>

    <h2 class="mb-3 font-semibold">{{ __('Reserva tu turno') }}</h2>

    @if (empty($services))
        <p class="rounded-2xl border border-dashed border-line-strong p-6 text-center text-sm text-muted">
            {{ __('Este negocio todavía no tiene servicios disponibles para reservar.') }}
        </p>
    @else
        <ul class="space-y-3">
            @foreach ($services as $service)
                <li class="rounded-2xl bg-surface-raised p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold">
                                {{ $service['name'] }}
                                @if ($service['mode'] === \App\Enums\ServiceMode::Virtual->value)
                                    <span class="ml-1 rounded bg-brand-100 px-2 py-0.5 text-xs text-brand-900 dark:bg-brand-900 dark:text-brand-100">{{ __('Virtual') }}</span>
                                @endif
                            </p>
                            <p class="text-sm text-muted">
                                {{ $service['duration_minutes'] }} min
                                @if ($service['price'] !== null)
                                    · ${{ number_format((float) $service['price'], 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                        <x-button :href="route('public.professional', [$business, $service['id']])" size="inline">
                            {{ __('Reservar') }}
                        </x-button>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    @if (! empty($reviews))
        <section class="mt-8">
            <h2 class="mb-3 font-semibold">{{ __('Reseñas') }}</h2>
            <ul class="space-y-3">
                @foreach ($reviews as $review)
                    <li class="rounded-2xl bg-surface-raised p-4 text-sm shadow-sm">
                        <p>
                            <span aria-hidden="true" class="text-amber-500">{{ str_repeat('★', $review['rating']) }}</span>
                            <span class="sr-only">{{ trans_choice(':count estrella|:count estrellas', $review['rating']) }}</span>
                            <span class="ml-1 font-medium">{{ $review['client_name'] }}</span>
                        </p>
                        <p class="mt-1 text-muted">{{ $review['comment'] }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <div class="mt-8 space-y-1 text-sm text-muted">
        @if ($business->address)
            <p><x-icon name="home" /> {{ $business->address }}</p>
        @endif
        @if ($business->whatsapp_phone)
            <p>
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $business->whatsapp_phone) }}"
                   class="text-brand-700 hover:underline dark:text-brand-400" rel="noopener">
                    <x-icon name="phone" /> WhatsApp
                </a>
            </p>
        @endif
    </div>
</x-public-layout>
