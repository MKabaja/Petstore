@extends('layouts.app')

@section('content')
    <form class="flex  md:flex-row flex-col gap-y-5  gap-x-5 w-full mb-20" method="GET" action="{{ route('pets.index') }}">
        <div class="flex flex-row gap-x-5 flex-1" > 
            <x-input name="tag" placeholder="Search by tag..." :value="$tag" class="w-full"/>
            <x-button type="submit" variant="ghost">Search</x-button>
        </div>
        
           
         <select name="status" class=" px-1 py-2 flex bg-surface text-muted border hover:border-accent hover:text-accent">
                <option value="available" {{ $status === 'available' ? 'selected' : '' }} >Available</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }} >Pending</option>
                <option value="sold" {{ $status === 'sold' ? 'selected' : '' }} >Sold</option>
        </select>
    </form>

    <ul class="md:grid md:grid-cols-3 flex flex-col gap-5">
    @forelse($pets as $pet)
        
            <x-card>
                
                 <a class=" hover:scale-105 duration-200" href="{{ route('pets.show', $pet->id) }}">
                    <img src="/images/placeholder.png" alt="No photo" class="md:w-30 md:h-30 w-40 object-cover rounded mx-auto">
                </a>
                <h3 class=" mx-auto text-2xl font-bold">{{ $pet->name }}</h3>
                <span class="text-xs text-text-muted">{{ $pet->categoryName }}</span>
                <x-badge status="{{ $pet->status->value }}"/>
                <div class="flex flex-row gap-2 flex-wrap gap">
                    @foreach($pet->tags as $tagName)
                        <span class="text-xs text-text-muted bg-elevated px-2 py-0.5 rounded">{{ $tag }}</span>
                    @endforeach
                </div>
                
                
                    <x-button href="{{ route('pets.edit', $pet->id) }}" variant="ghost" class="w-full">Edit</x-button>
                   
            </x-card>
        
            
    @empty
        <li>No pets found.</li>
    @endforelse
   
</ul>

<div class="mt-12 border-t border-gray-800 pt-6 flex justify-between">
    @if($page > 1)
        <a href="{{ route('pets.index', ['status' => $status, 'tag' => $tag, 'page' => $page - 1]) }}">← Previous</a>
    @else
        <span></span>
    @endif

    <span class="text-muted">Page {{ $page }}</span>

    @if($page * 20 < $total)
        <a href="{{ route('pets.index', ['status' => $status, 'tag' => $tag, 'page' => $page + 1]) }}">Next →</a>
    @endif
</div>
 
    
   
@endsection
