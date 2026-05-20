@props(['name', 'label' => null, 'placeholder' => '', 'value' => null])

<div class="w-full">
    @if($label)
        <label class="block text-sm font-bold mb-2 ">{{ $label }}</label>
    @endif

    <input
        name="{{ $name }}"
        value="{{ $value ?? (is_string(old($name)) ? old($name) : '') }}"
        placeholder="{{ $placeholder }}"
        
        {{ $attributes->merge(['class' => 'bg-surface px-4 py-2 rounded border border-border ']) }}
    >

    @error($name)
        <span class="text-red-400 text-sm">{{ $message }}</span>
    @enderror
</div>
