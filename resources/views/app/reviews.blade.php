<x-app-layout>
    <x-slot:title>{{ __('Reseñas') }}</x-slot:title>

    <h1 class="text-2xl font-bold">{{ __('Reseñas') }}</h1>
    <p class="mt-1 text-sm text-muted">
        {{ __('Las reseñas visibles aparecen en tu página pública. Puedes ocultar las que consideres inapropiadas.') }}
    </p>

    @if ($reviews->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-line-strong p-8 text-center text-muted">
            {{ __('Cuando tus clientes califiquen sus visitas, las verás aquí.') }}
        </div>
    @else
        <ul class="mt-6 space-y-3">
            @foreach ($reviews as $review)
                <li @class(['rounded-2xl bg-surface-raised p-4 shadow-sm', 'opacity-50' => $review->is_hidden])>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm">
                            <span aria-hidden="true" class="text-amber-500">{{ str_repeat('★', $review->rating) }}</span>
                            <span class="sr-only">{{ trans_choice(':count estrella|:count estrellas', $review->rating) }}</span>
                            <span class="ml-1 font-medium">{{ $review->client_name }}</span>
                            <span class="text-muted"> · {{ $review->booking->service->name ?? '' }} · {{ $review->created_at->isoFormat('D MMM YYYY') }}</span>
                        </p>
                        <form method="POST" action="{{ route('reviews.toggle', $review) }}">
                            @csrf @method('PATCH')
                            <button class="rounded-lg px-3 py-1.5 text-sm text-muted hover:bg-bg-subtle">
                                {{ $review->is_hidden ? __('Mostrar') : __('Ocultar') }}
                            </button>
                        </form>
                    </div>
                    @if ($review->comment)
                        <p class="mt-2 text-sm text-muted">{{ $review->comment }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</x-app-layout>
