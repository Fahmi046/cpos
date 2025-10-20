<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'POS App' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-100">
    <x-progress-bar color="emerald" />
    @auth
        <x-navbar />
    @endauth

    <main class="p-6">
        {{ $slot }}
    </main>

    @livewireScripts

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const bar = document.createElement("div");
            bar.className =
                "fixed top-0 left-0 h-1 bg-gradient-to-r from-sky-400 via-blue-500 to-cyan-600 z-50 transition-all duration-300 ease-out";
            bar.style.width = "0%";
            document.body.appendChild(bar);

            // Saat user klik link (navigasi)
            document.querySelectorAll("a[href]").forEach(link => {
                link.addEventListener("click", e => {
                    // Abaikan link yang buka tab baru atau anchor #
                    if (e.metaKey || e.ctrlKey || link.getAttribute("href").startsWith("#")) return;
                    bar.style.width = "40%";
                });
            });

            // Saat halaman mulai unload (navigasi)
            window.addEventListener("beforeunload", () => {
                bar.style.width = "100%";
            });

            // Saat halaman selesai dimuat
            window.addEventListener("load", () => {
                bar.style.width = "0%";
            });
        });
    </script>

</body>

</html>
