@php($local = $booking->starts_at->setTimezone($booking->business->timezone))

{{-- noindex: the URL carries the management token, so an indexed copy is a
     booking anyone can cancel. robots.txt disallows /t/, this is the belt. --}}
<x-public-layout :title="__('Your appointment').' — '.$booking->business->name" :business="$booking->business" :noindex="true">
    @if (session('status'))
        <p class="nexo-flash mb-4" role="status">{{ session('status') }}</p>
    @endif

    <div class="rounded-2xl bg-surface-raised p-6 shadow-sm">
        <p class="text-sm text-muted">{{ __('Your appointment at') }}</p>
        <h1 class="text-xl font-bold">{{ $booking->business->name }}</h1>

        <dl class="mt-4 space-y-1 text-sm">
            <div class="flex justify-between gap-4">
                <dt class="text-muted">{{ __('Service') }}</dt>
                <dd class="font-medium">{{ $booking->service->name }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-muted">{{ __('Date') }}</dt>
                <dd class="font-medium capitalize">{{ $local->isoFormat('dddd D [de] MMMM YYYY') }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-muted">{{ __('Time') }}</dt>
                <dd class="font-medium">{{ $local->format('H:i') }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-muted">{{ __('With') }}</dt>
                <dd class="font-medium">{{ $booking->professional->name }}</dd>
            </div>
            @if ($booking->service->price !== null)
                <div class="flex justify-between gap-4">
                    <dt class="text-muted">{{ __('Price') }}</dt>
                    <dd class="font-medium">${{ number_format((float) $booking->service->price, 0, ',', '.') }}</dd>
                </div>
            @endif
            <div class="flex justify-between gap-4">
                <dt class="text-muted">{{ __('Status') }}</dt>
                <dd class="font-medium">{{ $booking->status->label() }}</dd>
            </div>
        </dl>

        @if ($booking->status === \App\Enums\BookingStatus::Confirmed && $booking->service->mode === \App\Enums\ServiceMode::Virtual && $booking->service->video_link)
            <x-button :href="$booking->service->video_link" rel="noopener" class="mt-4">
                {{ __('Join the video call') }}
            </x-button>
        @endif

        @if ($booking->business->address)
            <p class="mt-4 text-sm text-muted"><x-icon name="home" /> {{ $booking->business->address }}</p>
        @endif

        @if ($booking->status === \App\Enums\BookingStatus::Confirmed)
            <div class="mt-5 border-t border-line pt-5 text-center">
                {{-- White in both themes on purpose: the QR is dark modules on a
                     light quiet zone, and inverting it stops scanners reading it. --}}
                <div class="mx-auto inline-block rounded-xl bg-white p-2 dark:bg-white">
                    {!! app(\App\Services\QrSvg::class)->forUrl(route('checkin', $token), 180) !!}
                </div>
                <p class="mt-2 text-xs text-muted">{{ __('Show this code on arrival to check in.') }}</p>
            </div>
        @endif
    </div>

    @if ($booking->clientCanManage())
        <div class="mt-4 flex gap-3">
            <a href="{{ route('booking.reschedule', $token) }}"
               class="flex-1 rounded-lg border border-brand-700 px-4 py-2 text-center text-sm font-semibold text-brand-700 hover:bg-bg-subtle dark:border-brand-400 dark:text-brand-400">
                {{ __('Reschedule') }}
            </a>
            <form method="POST" action="{{ route('booking.cancel', $token) }}" class="flex-1"
                  x-data x-on:submit="if (! confirm(@js(__('Cancel your appointment?')))) $event.preventDefault()">
                @csrf
                <button class="w-full rounded-lg border border-danger px-4 py-2 text-sm font-semibold text-danger hover:bg-danger-subtle">
                    {{ __('Cancel appointment') }}
                </button>
            </form>
        </div>
        <p class="mt-2 text-center text-xs text-muted">
            {{ __('You can cancel or reschedule up to :hours h before.', ['hours' => $booking->service->cancellation_hours]) }}
        </p>
    @elseif ($booking->status === \App\Enums\BookingStatus::Confirmed)
        <p class="mt-4 text-center text-xs text-muted">
            {{ __('The window to cancel or reschedule online has passed. Contact the business.') }}
        </p>
    @endif

    @if ($booking->canBeReviewed())
        <section class="mt-6 rounded-2xl bg-surface-raised p-5 shadow-sm">
            <h2 class="font-semibold">{{ __('How was your experience?') }}</h2>
            <form method="POST" action="{{ route('booking.review', $token) }}" class="mt-3 space-y-3">
                @csrf
                <fieldset>
                    <legend class="sr-only">{{ __('Rating') }}</legend>
                    <div class="rating gap-1 text-3xl">
                        @foreach ([5, 4, 3, 2, 1] as $stars)
                            <label>
                                <input type="radio" name="rating" value="{{ $stars }}" class="sr-only" @checked(old('rating') == $stars) required>
                                <span aria-hidden="true">★</span>
                                <span class="sr-only">{{ trans_choice(':count star|:count stars', $stars) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('rating')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div>
                    <label for="comment" class="mb-1 block text-sm font-medium">{{ __('Comment (optional)') }}</label>
                    <textarea id="comment" name="comment" rows="3" maxlength="500"
                              class="w-full rounded-lg border-control bg-surface text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('comment') }}</textarea>
                    @error('comment')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <x-button>{{ __('Submit review') }}</x-button>
            </form>
        </section>
    @elseif ($booking->review)
        <p class="mt-4 rounded-2xl bg-surface-raised p-4 text-center text-sm text-muted shadow-sm">
            {{ __('You rated this visit :rating out of 5. Thank you!', ['rating' => $booking->review->rating]) }}
        </p>
    @endif

    <p class="mt-4 text-center text-xs text-muted">
        {{ __('Save this link: it\'s your receipt and where you manage your appointment.') }}
    </p>
</x-public-layout>
