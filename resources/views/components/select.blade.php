@props(['label', 'name', 'options', 'selected' => null])

<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}"
            @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
            {{ $attributes->class([
                'w-full rounded-lg border-control bg-surface text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500',
            ]) }}>
        @foreach ($options as $key => $option)
            <option value="{{ $key }}" @selected(old($name, $selected) == $key)>{{ $option }}</option>
        @endforeach
    </select>
    @error($name)
        <p id="{{ $name }}-error" class="mt-1 text-sm text-danger">{{ $message }}</p>
    @enderror
</div>
