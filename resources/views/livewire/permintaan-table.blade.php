<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Cari No permintaan atau Keterangan"
            class="border border-gray-300 rounded-md px-4 py-2 w-full md:w-1/3 focus:ring-2 focus:ring-indigo-300 focus:outline-none transition">

        <div class="flex gap-2 mb-4">
            <button wire:click="exportExcelDetailed" class="px-3 py-2 bg-indigo-600 text-white rounded">Export
                (Detail)</button>
            <button wire:click="exportExcelSummary" class="px-3 py-2 bg-gray-600 text-white rounded">Export
                (Ringkas)</button>
        </div>
    </div>

    <h2 class="text-2xl font-bold mb-4 text-gray-800">📦 Daftar permintaan Stok</h2>

    <!-- Tabel -->
    <div class="overflow-x-auto bg-white rounded-md shadow-md">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-center">No</th>
                    <th class="px-4 py-3">No permintaan</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Outlet</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Detail permintaan</th>
                    <th class="px-4 py-3">Status</th> <!-- 👈 kolom baru -->
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permintaanList as $permintaan)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- No -->
                        <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>

                        <!-- No permintaan -->
                        <td class="px-4 py-3 font-medium">{{ $permintaan->no_permintaan }}</td>

                        <!-- Tanggal -->
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($permintaan->tanggal)->format('d M Y') }}</td>

                        <!-- Outlet -->
                        <td class="px-4 py-3">{{ $permintaan->outlet->nama_outlet ?? '-' }}</td>

                        <!-- Keterangan -->
                        <td class="px-4 py-3">{{ $permintaan->keterangan }}</td>

                        <!-- Detail permintaan -->
                        <td class="px-4 py-3">
                            <div class="space-y-2">
                                @foreach ($permintaan->details as $detail)
                                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg shadow-sm">
                                        <div class="flex justify-between items-center">
                                            <span class="font-semibold text-gray-800">
                                                {{ $detail->obat->nama_obat ?? '-' }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-600">
                                                {{ $detail->qty_minta }}
                                                {{ $detail->utuhan ? $detail->satuan->nama_satuan ?? '-' : $detail->sediaan->nama_sediaan ?? '-' }}
                                            </span>
                                            <span class="text-sm font-bold text-indigo-600">
                                                Rp {{ number_format($detail->harga, 0) }}
                                            </span>
                                        </div>
                                        <div class="mt-2 grid grid-cols-3 gap-2 text-xs text-gray-500">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-4 py-3">
                            @if ($permintaan->status === 'pending')
                                <span
                                    class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded">Pending</span>
                            @elseif ($permintaan->status === 'sebagian')
                                <span
                                    class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded">Sebagian</span>
                            @elseif ($permintaan->status === 'selesai')
                                <span
                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Selesai</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded">-</span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-4 py-3 flex justify-center space-x-2">
                            @if ($permintaan->status === 'pending')
                                <!-- Tombol Hapus -->
                                <button wire:click="delete({{ $permintaan->id }})"
                                    class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition"
                                    title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <!-- Tombol Tambah Mutasi -->
                                <a href="{{ route('mutasi.create', ['permintaan_id' => $permintaan->id]) }}"
                                    class="p-2 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition"
                                    title="Tambahkan Mutasi">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </a>
                            @elseif ($permintaan->status === 'sebagian')
                                <!-- Tombol Tambah Mutasi -->
                                <a href="{{ route('mutasi.create', ['permintaan_id' => $permintaan->id]) }}"
                                    class="p-2 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition"
                                    title="Tambahkan Mutasi">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-gray-500">Belum ada permintaan stok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Tambahkan pagination di sini -->
        <div class="mt-6 mb-4 flex justify-center">
            {{ $permintaanList->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
