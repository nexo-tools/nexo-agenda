<x-app-layout>
    <x-slot:title>{{ __('Reviews') }}</x-slot:title>

    <h1 class="text-2xl font-bold">{{ __('Reviews') }}</h1>
    <p class="mt-1 text-sm text-muted">
        {{ __('Visible reviews appear on your public page. You can hide any you consider inappropriate.') }}
    </p>

    @if ($reviews->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-line-strong p-8 text-center text-muted">
            {{ __('When your clients rate their visits, you\'ll see them here.') }}
        </div>
    @else
        <ul class="mt-6 space-y-3">
            @foreach ($reviews as $review)
                <li @class(['rounded-2xl bg-surface-raised p-4 shadow-sm', 'opacity-50' => $review->is_hidden])>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm">
                            <span aria-hidden="true" class="text-amber-500">{{ str_repeat('★', $review->rating) }}</span>
                            <span class="sr-only">{{ trans_choice(':count star|:count stars', $review->rating) }}</span>
                            <span class="ml-1 font-medium">{{ $review->client_name }}</span>
                            <span class="text-muted"> · {{ $review->booking->service->name ?? '' }} · {{ $review->created_at->isoFormat('D MMM YYYY') }}</span>
                        </p>
                        <form method="POST" action="{{ route('reviews.toggle', $review) }}">
                            @csrf @method('PATCH')
                            <button class="nexo-btn nexo-btn--sm text-muted hover:bg-bg-subtle">
                                {{ $review->is_hidden ? __('Show') : __('Hide') }}
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
