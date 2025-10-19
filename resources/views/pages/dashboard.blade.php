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

            <!-- Dashboard Mutasi -->
            <livewire:permintaan-table />

            <!-- Tabel Permintaan Mutasi sudah ada di Livewire Component -->
        </div>
    </div>
</x-app-layout>
