@props(['variant' => 'primary'])

@php
$classes = match($variant) {
    'danger' => 'bg-red-500  hover:bg-red-600',
    'ghost' => 'bg-transparent text-muted border hover:border-accent hover:text-accent',
    default => 'bg-accent  hover:bg-accent-hover',
};
@endphp

<button {{ $attributes->merge(['class' => "duration-200 border-border rounded px-4 py-2 $classes"]) }}>
    {{ $slot }}
</button>
