<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <input type="text" wire:model.live="search" placeholder="Cari kode / nama obat"
            class="border border-gray-300 rounded-md px-4 py-2 w-full md:w-1/3 focus:ring-2 focus:ring-indigo-300 focus:outline-none transition">

        <div class="flex space-x-2">
            <button wire:click="exportExcel"
                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow-md transition">
                Export
            </button>
            <button wire:click="downloadTemplate"
                class="flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md shadow-md transition">
                Template
            </button>
            <input type="file" wire:model="file" accept=".xlsx,.xls,.csv" class="border rounded px-2 py-1">
            <button wire:click="importExcel"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-md transition">
                Upload
            </button>
        </div>
    </div>

    <h2 class="text-2xl font-bold mb-4 text-gray-800">💊 Daftar Obat</h2>

    <!-- Tabel -->
    <div class="overflow-x-auto bg-white rounded-md shadow-md">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-center">No</th>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Bentuk</th>
                    <th class="px-4 py-3">Satuan</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($obats as $obat)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium">{{ $obat->kode_obat }}</td>
                        <td class="px-4 py-3">{{ $obat->nama_obat }}</td>
                        <td class="px-4 py-3">{{ $obat->kategori->nama_kategori ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $obat->sediaan->nama_sediaan ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $obat->satuan->nama_satuan ?? '-' }}</td>

                        <!-- Aksi -->
                        <td class="px-4 py-3 flex justify-center space-x-2">
                            <button wire:click="$dispatch('edit-obat', { id: {{ $obat->id }} })"
                                class="p-2 bg-yellow-100 text-yellow-600 rounded-md hover:bg-yellow-200 transition"
                                title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm-2 2h.01" />
                                </svg>
                            </button>

                            <button wire:click="delete({{ $obat->id }})"
                                class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition"
                                title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </td>
                    </tr>

                    <!-- Detail Obat Card -->
                    <tr>
                        <td colspan="7" class="px-4 py-3">
                            <div
                                class="p-3 bg-gray-50 border border-gray-200 rounded-lg shadow-sm grid grid-cols-3 gap-2 text-xs text-gray-500">
                                <div>💊 Komposisi: {{ $obat->komposisi->nama_komposisi ?? '-' }}</div>
                                <div>🏭 Pabrik: {{ $obat->pabrik->nama_pabrik ?? '-' }}</div>
                                <div>💰 Harga: Rp {{ number_format($obat->harga_jual, 0) }}</div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">Belum ada data obat</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-6 mb-4 flex justify-center">
            {{ $obats->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
