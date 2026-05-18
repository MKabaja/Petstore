@extends('layouts.app')

@section('content')
<x-card>
     <div>
        <a href="{{ route('pets.index') }}" variant="ghost" class="border border-border px-1 py-1  rounded text-text-muted hover:text-text-secondary hover:border-text-secondary duration-200">Back</a>
        <x-delete-form :route="route('pets.destroy', $pet->id)"/>
    </div>
    <div class="flex flex-col md:grid md:grid-cols-2 ">
        <div>
            @foreach ($pet->photoUrls  as $url)
                <img src="{{ $url }}" alt="{{ $pet->name }}" onerror="this.src='/images/placeholder.png'"class="w-60 h-60 object-cover rounded"> 
                
            @endforeach
        </div>
        <div class="flex flex-col justify-center  items-center gap-3">
            <div class="mb-10 flex flex-col items-center gap-2">
                <h2 class="text-5xl font-bold ">{{ $pet->name }}</h2>
                <span class="text-sm text-text-muted">{{ $pet->categoryName }}</span>
            </div >
            <div class="flex flex-row gap-2 flex-wrap">
                @foreach($pet->tags as $tagName)
                <span class="text-xs text-text-muted bg-elevated px-2 py-0.5 rounded">{{'# '. $tagName }}</span>
                @endforeach
            </div>
            <x-badge status="{{ $pet->status->value }}" class="w-full text-center"/>
        </div>
    </div>
    <x-button href="{{ route('pets.edit', $pet->id) }}" variant="ghost" class="text-center">Edit</x-button>
</x-card>
@endsection

        
       
           
            
           