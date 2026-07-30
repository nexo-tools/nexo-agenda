@props(['active' => false])

<a {{ $attributes->class([
    'rounded-lg px-3 py-2 text-sm',
    'bg-brand-100 font-medium text-brand-900 dark:bg-brand-900 dark:text-brand-100' => $active,
    'text-muted hover:bg-bg-subtle' => ! $active,
]) }} @if ($active) aria-current="page" @endif>
    {{ $slot }}
</a>
