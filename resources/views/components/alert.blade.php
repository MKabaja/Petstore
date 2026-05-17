@props(['type' => 'success', 'message'])

@php
$classes = match($type) {
    'error'   => 'bg-error/20 border-error text-error',
    default   => 'bg-success/20 border-success text-success',
};
@endphp

<span class="mb-4 rounded border px-4 py-2 {{ $classes }}">
    {{ $message }}
</span>
