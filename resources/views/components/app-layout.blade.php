<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'POS App' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-100">

    <x-navbar />

    <main class="p-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>

</html>
