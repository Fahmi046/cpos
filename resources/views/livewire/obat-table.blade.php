<div class="min-h-screen p-6 bg-gray-50">
    <!-- Header -->
    <div class="flex flex-col items-start justify-between gap-4 mb-6 md:flex-row md:items-center">
        <div class="relative mb-4 md:w-1/3">
            <input type="text" wire:model.live="search" placeholder="Cari nama obat"
                class="w-full px-4 py-2 transition border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-300 focus:outline-none">

            @if (!empty($results))
                <ul
                    class="absolute z-10 w-full mt-1 overflow-y-auto bg-white border border-gray-300 rounded-md shadow-lg max-h-60">
                    @foreach ($results as $item)
                        <li wire:click="selectObat('{{ $item->nama_obat }}')"
                            class="px-4 py-2 cursor-pointer hover:bg-indigo-100">
                            {{ $item->nama_obat }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="flex space-x-2">
            <button wire:click="exportExcel"
                class="flex items-center gap-2 px-4 py-2 text-white transition bg-green-600 rounded-md shadow-md hover:bg-green-700">
                Export
            </button>
            <button wire:click="downloadTemplate"
                class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-600 rounded-md shadow-md hover:bg-gray-700">
                Template
            </button>
            <input type="file" wire:model="file" accept=".xlsx,.xls,.csv" class="px-2 py-1 border rounded">
            <button wire:click="importExcel"
                class="flex items-center gap-2 px-4 py-2 text-white transition bg-blue-600 rounded-md shadow-md hover:bg-blue-700">
                Upload
            </button>
        </div>
    </div>

    <h2 class="mb-4 text-2xl font-bold text-gray-800">💊 Daftar Obat</h2>

    <!-- Tabel -->
    <div class="overflow-x-auto bg-white rounded-md shadow-md">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
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
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium">{{ $obat->kode_obat }}</td>
                        <td class="px-4 py-3">{{ $obat->nama_obat }}</td>
                        <td class="px-4 py-3">{{ $obat->kategori->nama_kategori ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $obat->sediaan->nama_sediaan ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $obat->satuan->nama_satuan ?? '-' }}</td>

                        <!-- Aksi -->
                        <td class="flex justify-center px-4 py-3 space-x-2">
                            <button wire:click="$dispatch('edit-obat', { id: {{ $obat->id }} })"
                                class="p-2 text-yellow-600 transition bg-yellow-100 rounded-md hover:bg-yellow-200"
                                title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm-2 2h.01" />
                                </svg>
                            </button>

                            <button wire:click="delete({{ $obat->id }})"
                                class="p-2 text-red-600 transition bg-red-100 rounded-md hover:bg-red-200"
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
                                class="grid grid-cols-3 gap-2 p-3 text-xs text-gray-500 border border-gray-200 rounded-lg shadow-sm bg-gray-50">
                                <div>💊 Komposisi: {{ $obat->komposisi->nama_komposisi ?? '-' }}</div>
                                <div>🏭 Pabrik: {{ $obat->pabrik->nama_pabrik ?? '-' }}</div>
                                <div>💰 Harga: Rp {{ number_format($obat->harga_jual, 0) }}</div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-4 text-center text-gray-500">Belum ada data obat</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex justify-center mt-6 mb-4">
            {{ $obats->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
