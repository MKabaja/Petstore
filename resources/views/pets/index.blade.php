@extends('layouts.app')

@section('content')
   <x-alert type="success" message="Pet created successfully." />
    <x-alert type="error" message="Something went wrong." />
    <x-badge status="available" />
    <x-badge status="pending" />
    <x-badge status="sold" />
    <x-button>Primary</x-button>
    <x-button variant="danger">Danger</x-button>
    <x-button variant="ghost">Ghost</x-button>
    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <x-card>
    <h2>{{ 'imie' }}</h2>
     <x-badge status="available" />
    <p>{{ 'opis' }}</p>
</x-card>
   <x-card>
    <h2>{{ 'imie' }}</h2>
     <x-badge status="available" />
    <p>{{ 'opis' }}</p>
</x-card>
   <x-card>
    <h2>{{ 'imie' }}</h2>
     <x-badge status="available" />
    <p>{{ 'opis' }}</p>
</x-card>
    </div>
   
@endsection
