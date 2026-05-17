@props(['status'])

@php

$classes = match($status) {
    'available' => 'bg-available/50 border-available-border text-available',
    'pending'   => 'bg-pending/50 border-pending-border text-pending',
    'sold'      => 'bg-sold/50 border-sold-border text-sold',
    default     => 'bg-gray-900/50 border-gray-800 text-gray-200',
};

@endphp

<span class="w-fit mb-4 rounded-md uppercase border px-4 py-1 {{ $classes }}">
    {{ $status }}
</span>