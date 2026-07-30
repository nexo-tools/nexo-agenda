{{-- Booking status pill. One place decides which role token a status wears, so
     the three views that show it cannot drift apart — and every pair used here
     (*-subtle / *-subtle-fg) already flips with the theme, which the hand-rolled
     bg-emerald-100/text-emerald-900 chips this replaces did not. --}}
@props(['status', 'size' => 'sm'])

@php
    $tone = match ($status) {
        \App\Enums\BookingStatus::Confirmed => 'bg-primary-subtle text-primary-subtle-fg',
        \App\Enums\BookingStatus::Attended => 'bg-success-subtle text-success-subtle-fg',
        \App\Enums\BookingStatus::NoShow => 'bg-danger-subtle text-danger-subtle-fg',
        \App\Enums\BookingStatus::Cancelled => 'bg-bg-subtle text-muted',
    };
@endphp

<span {{ $attributes->class([
    'rounded',
    'px-2 py-0.5 text-xs' => $size === 'sm',
    'px-2 py-1 text-sm' => $size === 'md',
    $tone,
]) }}>{{ $status->label() }}</span>
