@extends('layouts.app')

@section('content')
<x-card>
    <h2 class="text-3xl font-bold mb-6 text-center">Edit Pet</h2>
    <form id="edit-pet-form" data-pet-form method="POST" action="{{ route('pets.update', $pet->id) }}" class="md:grid flex flex-col md:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div class="col-span-1 space-y-5">
            <div class="flex flex-col gap-2">
                <x-input label="Name" name="name" id="name" placeholder="Pet name" required 
                    value="{{ old('name') ?? $pet->name }}" class="w-full"/>
                
                <x-input label="Category" name="category_name" id="category-name" placeholder="Pet category"
                    value="{{ old('category_name') ?? $pet->categoryName }}" class="w-full"/>
            </div>
            <div class="flex flex-col gap-2">
                <label for="status" class="font-semibold">Status</label>
                <select name="status" id="status" class="px-1 py-2 flex bg-surface text-muted border hover:border-accent hover:text-accent">
                    @php $currentStatus = old('status') ?? $pet->status->value; @endphp
                    <option value="available" @selected($currentStatus === 'available')>Available</option>
                    <option value="pending"   @selected($currentStatus === 'pending')>Pending</option>
                    <option value="sold"      @selected($currentStatus === 'sold')>Sold</option>
                </select>
            </div>

            <div id="tags-container" data-tags="{{ json_encode($pet->tags) }}" class="flex flex-col gap-2">
                <label for="tags" class="font-semibold">Tags</label>
                <div class="flex gap-5 relative">
                    <x-input name="tags" id="tags" placeholder="e.g. cute, small, friendly" class="w-full"/>
                    <button type="button" aria-label="Add tag" class="border border-border border-dotted rounded px-4 py-1 text-2xl text-center text-text-muted hover:text-text-primary duration-200 hover:border-text-secondary">+</button>
                </div>
                <p data-error class="hidden text-xs text-error"> </p>
                <ul id="tags-list" class="grid grid-cols-5 gap-1">
                   
                </ul>
            </div>
        </div>
        <div id="photos-container" data-photo-urls="{{ json_encode($pet->photoUrls) }}" class="flex flex-col gap-2">
            <label for="photos" class="font-semibold">Photos</label>
            <div class="flex gap-5">
                <x-input name="photos" id="photos" placeholder="http://" class="w-full"/>
                <button type="button" aria-label="Add photo URL" class="border border-border border-dotted rounded px-4 py-1 text-2xl text-center text-text-muted hover:text-text-primary duration-200 hover:border-text-secondary">+</button>
            </div>
            <p data-error class="hidden text-xs text-error"> </p>
            <ul id="photos-list" class="mt-2 flex flex-col gap-2 text-xs px-2">
              
            </ul>
        </div>
        <x-button type="submit" variant="primary" class="col-span-2">Update Pet</x-button>
        <x-button type="button" href="{{ route('pets.show', $pet->id) }}" variant="ghost" class="col-span-2 text-center">Cancel</x-button>
    </form>
</x-card>
@push('scripts')
    @vite(['resources/js/tags.ts', 'resources/js/photoUrls.ts'])
@endpush
@endsection
