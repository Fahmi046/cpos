<div class="p-4 bg-white rounded shadow mt-4">
    <h2 class="text-lg font-bold mb-4">Daftar Obat</h2>

    {{-- Kolom Pencarian --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
        <input type="text" wire:model.live="search" placeholder="Cari obat berdasarkan kode / nama..."
            class="w-full md:w-1/3 p-2 border rounded shadow-sm focus:ring focus:ring-blue-200">

        <div class="flex space-x-2">
            {{-- Tombol Export --}}
            <button wire:click="exportExcel" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                Export Excel
            </button>
            {{-- Tombol Download Template --}}
            <button wire:click="downloadTemplate"
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded shadow">
                Download Template
            </button>

            {{-- Input Upload File --}}
            <input type="file" wire:model="file" accept=".xlsx,.xls,.csv" class="border rounded px-2 py-1">
            <button wire:click="importExcel" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow">
                Upload
            </button>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="p-2 bg-green-100 text-green-700 mb-4 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- Loading State --}}
    <div wire:loading class="mb-2 text-blue-600 text-sm">
        Memuat data...
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full border border-gray-300 text-sm">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="border p-2">Kode</th>
                    <th class="border p-2">Nama</th>
                    <th class="border p-2">Kategori</th>
                    <th class="border p-2">Bentuk</th>
                    <th class="border p-2">Komposisi</th>
                    <th class="border p-2">Satuan</th>
                    <th class="border p-2">Pabrik</th>
                    <th class="border p-2">Harga Jual</th>
                    <th class="border p-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($obats as $obat)
                    <tr class="hover:bg-gray-50">
                        <td class="border p-2">{{ $obat->kode_obat }}</td>
                        <td class="border p-2">{{ $obat->nama_obat }}</td>
                        <td class="border p-2">{{ $obat->kategori->nama_kategori ?? '-' }}</td>
                        <td class="border p-2">{{ $obat->sediaan->nama_sediaan ?? '-' }}</td>
                        <td class="border p-2">{{ $obat->komposisi->nama_komposisi ?? '-' }}</td>
                        <td class="border p-2">{{ $obat->satuan->nama_satuan ?? '-' }}</td>
                        <td class="border p-2">{{ $obat->pabrik->nama_pabrik ?? '-' }}</td>
                        <td class="border p-2">Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}</td>
                        <td class="border p-2 text-center">
                            <div class="flex justify-center space-x-2">
                                <button wire:click="$dispatch('edit-obat', { id: {{ $obat->id }} })"
                                    class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $obat->id }})"
                                    class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center p-4">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $obats->links() }}
        </div>
    </div>
</div>
