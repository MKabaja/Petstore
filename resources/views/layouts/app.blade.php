<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Petstore')</title>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body class="min-h-screen  ">

    <nav class="border-b border-gray-800 px-6 py-4">
        <div class="mx-auto max-w-5xl flex items-center justify-between">
            <a href="{{ route('pets.index') }}"
               class="text-lg font-bold text-orange-500 hover:text-orange-400">
                Petstore
            </a>
            <a href="{{ route('pets.create') }}"
               class="rounded bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600">
                Add Pet
            </a>
        </div>
    </nav>

    <main class="mx-auto max-w-5xl px-6 py-8 ">

        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        @if(session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        @yield('content')

    </main>

</body>
</html>
