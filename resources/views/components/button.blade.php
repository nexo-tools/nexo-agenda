@props(['type' => 'submit'])

<button type="{{ $type }}" {{ $attributes->class([
    'inline-flex w-full items-center justify-center rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2',
]) }}>
    {{ $slot }}
</button>
