{{-- The one button in the app. Renders an <a> when :href is passed, so a link
     that looks like a button stops being a hand-copied class string that forgets
     the focus ring — which is exactly how seven of them lost it.

     variant: primary | outline | ghost | danger
     size:    md (default, full width in forms) | inline (hugs its content)

     Filled surfaces take text-brand-fg, not text-white: on a business storefront
     the accent may be light, and that token is the contrast-checked foreground. --}}
@props([
    'type' => 'submit',
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $classes = implode(' ', [
        'inline-flex items-center justify-center gap-1.5 rounded-lg text-sm font-semibold',
        // 44px minimum touch target, the same floor .nexo-btn holds.
        'min-h-11',
        'focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2',
        'transition-colors disabled:cursor-not-allowed disabled:opacity-55',
        match ($size) {
            'inline' => 'px-4 py-2',
            default => 'w-full px-4 py-2.5',
        },
        match ($variant) {
            'outline' => 'border border-brand-700 text-brand-700 hover:bg-brand-50 dark:border-brand-400 dark:text-brand-400',
            'ghost' => 'border border-control text-ink hover:bg-bg-subtle',
            'danger' => 'border border-danger text-danger hover:bg-danger-subtle',
            default => 'bg-brand-700 text-brand-fg hover:bg-brand-800',
        },
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([$classes]) }}>{{ $slot }}</a>
@elseif ($type === 'submit')
    {{-- Double-submit guard. Creating a booking hits availability checks and sends
         mail, so an impatient second click used to create a second booking with
         nothing in the client stopping it. The listener sits on the form (submit
         does not bubble to the button) and stands down if something already
         cancelled the submission, e.g. a confirm() the user declined. --}}
    <button type="submit"
            x-data="{ sending: false }"
            x-init="$el.form?.addEventListener('submit', (event) => { if (! event.defaultPrevented) sending = true })"
            x-bind:disabled="sending"
            x-bind:aria-busy="sending"
            {{ $attributes->class([$classes]) }}>{{ $slot }}</button>
@else
    <button type="{{ $type }}" {{ $attributes->class([$classes]) }}>{{ $slot }}</button>
@endif
