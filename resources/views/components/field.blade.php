@props(['label', 'name', 'type' => 'text', 'value' => null, 'required' => false, 'autocomplete' => null])

<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
           value="{{ $type !== 'password' ? old($name, $value) : '' }}"
           @if ($required) required @endif
           @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
           @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
           {{ $attributes->class([
               'w-full rounded-lg border-control bg-surface text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500',
           ]) }}>
    @error($name)
        <p id="{{ $name }}-error" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
