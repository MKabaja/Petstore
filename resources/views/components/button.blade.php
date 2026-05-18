@props(['variant' => 'primary','href' => null])

@php
$classes = match($variant) {
    'danger' => 'bg-red-500  hover:bg-red-600',
    'ghost' => 'bg-transparent text-muted border hover:border-accent hover:text-accent',
    default => 'bg-accent hover:bg-accent-light',
};
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "duration-200 border-border rounded px-4 py-2 $classes"]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => "duration-200 border-border rounded px-4 py-2 $classes"]) }}>
        {{ $slot }}
    </button>
@endif
