<x-app-layout :title="'Halaman Utama'">
    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <!-- Selamat Datang -->
            <div class="p-6 mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <h1 class="mb-2 text-2xl font-bold">Selamat Datang di Apotek Sahabat</h1>
                <p class="mb-2 text-gray-700">
                    Ini adalah sistem informasi <strong>Apotek Sahabat</strong> berbasis Website.
                </p>
                <p class="text-gray-600">
                    Sistem ini memudahkan pengelolaan stok, mutasi obat, permintaan, dan laporan secara real-time.
                </p>
            </div>

            {{-- Outlet hanya bisa melihat form permintaan --}}
            @if (auth()->user()->role === 'outlet')
                <div class="p-6 mb-6 bg-white shadow-sm sm:rounded-lg">
                    <h2 class="mb-4 text-xl font-semibold text-gray-800">Daftar Stok Outlet</h2>
                    <livewire:stok-outlet-table />
                </div>
            @endif

            {{-- Admin / Gudang hanya bisa melihat tabel permintaan --}}
            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'gudang')
                <div class="p-6 mb-6 bg-white shadow-sm sm:rounded-lg">
                    <h2 class="mb-4 text-xl font-semibold text-gray-800">Daftar Permintaan Stok</h2>
                    <livewire:permintaan-table />
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
