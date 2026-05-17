@props(['type' => 'success', 'message'])

@php
$classes = match($type) {
    'error'   => 'bg-error/20 border-error text-error',
    default   => 'bg-success/20 border-success text-success',
};
@endphp

<span data-alert class="fixed left-1/2 top-4 -translate-x-1/2 z-50 rounded border px-4 py-2 transition-all duration-200 {{ $classes }}">
    {{ $message }}
</span>
