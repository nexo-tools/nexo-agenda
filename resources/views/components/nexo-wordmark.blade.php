{{-- Wordmark lockup: isotype tile + "Nexo X". Copy nexo-brand/marks/<tool>.svg
     into public/ecosystem/ (or public/favicon.svg) and point $mark at it. --}}
@props([
    'href' => '/',
    'label' => 'Nexo',
    'mark' => '/favicon.svg',
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'nexo-header__brand']) }} aria-label="{{ $label }}">
    <img src="{{ $mark }}" alt="" width="32" height="32" />
    <span>{{ $label }}</span>
</a>
