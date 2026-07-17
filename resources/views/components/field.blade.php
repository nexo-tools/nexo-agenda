@props(['label', 'name', 'type' => 'text', 'value' => null, 'required' => false, 'autocomplete' => null])

<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
           value="{{ $type !== 'password' ? old($name, $value) : '' }}"
           @if ($required) required @endif
           @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
           {{ $attributes->class([
               'w-full rounded-lg border-slate-300 bg-white text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200',
           ]) }}>
    @error($name)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
