@extends('layouts.app')

@section('content')
<x-card>
    <h2 class="text-3xl font-bold mb-6 text-center">Add New Pet</h2>
    <form method="POST" action="{{ route('pets.store') }}" class="md:grid  flex flex-col md:grid-cols-2 gap-4">
        @csrf
        <div class="col-span-1 space-y-5">
            <div class="flex flex-col gap-2">
                <x-input label="Name" name="name" id="name" placeholder="Pet name" required class="w-full"/>
                <x-input label="Category" name="category_name" id="category-name" placeholder="Pet category" class="w-full"/>
            </div>
            <div class="flex flex-col gap-2">
                <label for="status" class="font-semibold">Status</label>
                <select name="status" id="status" class=" px-1 py-2 flex bg-surface text-muted border hover:border-accent hover:text-accent">
                    <option value="available">Available</option>
                    <option value="pending">Pending</option>
                    <option value="sold">Sold</option>
                </select>
            </div>
            
            <div id="tags-container" class="flex flex-col gap-2">
                <label for="tags" class="font-semibold">Tags</label>
                <div class="flex gap-5 relative">
                    <x-input name="tags" id="tags" placeholder="e.g. cute, small, friendly" class="w-full"/>
                    <button type="button" class="border  border-border border-dotted rounded px-4 py-1 text-2xl text-center text-text-muted hover:text-text-primary duration-200 hover:border-text-secondary" >+</button>
                    
                </div>
                <p data-error class="text-error text-xs hidden"> </p>
                <ul id="tags-list" class="grid grid-cols-5 gap-1"></ul>
            </div>
        </div>
        <div id="photos-container" class="flex flex-col gap-2">
            <label for="photos" class="font-semibold">Photos</label>
            <div class="flex gap-5">
                <x-input name="photos" id="photos" placeholder="http://" class="w-full"/>
                <button type="button" class="border  border-border border-dotted rounded px-4 py-1 text-2xl text-center text-text-muted hover:text-text-primary duration-200 hover:border-text-secondary" >+</button>
            </div>
            <p data-error class="text-error text-xs hidden"> </p>
            <ul id="photos-list" class="mt-2 flex flex-col gap-2 text-xs px-2"></ul>
        </div>
        <x-button type="submit" variant="primary" class="col-span-2">Create Pet</x-button>
        <x-button type="button" href="{{ route('pets.index') }}" variant="ghost" class="col-span-2 text-center">Cancel</x-button>
    </form>
</x-card>
@push('scripts')
    @vite(['resources/js/tags.ts', 'resources/js/photoUrls.ts'])
@endpush
@endsection    