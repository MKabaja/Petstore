@props(['route'])

<form action="{{ $route }}" method="POST"
      onsubmit="return confirm('Are you sure?')">
    @csrf
    @method('DELETE')
    <button type="submit" class=" duration-200 absolute top-2 right-2 bg-surface border border-border rounded px-2 text-text-muted hover:border-red-500 hover:text-red-300">
        X
    </button>
</form>
