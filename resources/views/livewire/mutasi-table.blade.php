<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Cari No Mutasi atau Keterangan"
            class="border border-gray-300 rounded-md px-4 py-2 w-full md:w-1/3 focus:ring-2 focus:ring-indigo-300 focus:outline-none transition">

        <button wire:click="exportExcel"
            class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow-md transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7h12M8 12h12M8 17h12M3 7h.01M3 12h.01M3 17h.01" />
            </svg>
            Export
        </button>
    </div>

    <h2 class="text-2xl font-bold mb-4 text-gray-800">📦 Daftar Mutasi Stok</h2>

    <!-- Tabel -->
    <div class="overflow-x-auto bg-white rounded-md shadow-md">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-center">No</th>
                    <th class="px-4 py-3">No Mutasi</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Outlet</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Detail Mutasi</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mutasiList as $mutasi)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- No -->
                        <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>

                        <!-- No Mutasi -->
                        <td class="px-4 py-3 font-medium">{{ $mutasi->no_mutasi }}</td>

                        <!-- Tanggal -->
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($mutasi->tanggal)->format('d M Y') }}</td>

                        <!-- Outlet -->
                        <td class="px-4 py-3">{{ $mutasi->outlet->nama_outlet ?? '-' }}</td>

                        <!-- Keterangan -->
                        <td class="px-4 py-3">{{ $mutasi->keterangan }}</td>

                        <!-- Detail Mutasi -->
                        <td class="px-4 py-3">
                            <div class="space-y-2">
                                @foreach ($mutasi->details as $detail)
                                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg shadow-sm">
                                        <!-- Baris utama -->
                                        <div class="flex justify-between items-center">
                                            <span class="font-semibold text-gray-800">
                                                {{ $detail->obat->nama_obat ?? '-' }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-600">
                                                Qty: {{ $detail->qty }}
                                            </span>
                                            <span class="text-sm font-bold text-indigo-600">
                                                Rp {{ number_format($detail->jumlah, 0) }}
                                            </span>
                                        </div>

                                        <!-- Baris tambahan -->
                                        <div class="mt-2 grid grid-cols-3 gap-2 text-xs text-gray-500">
                                            <div>Batch: {{ $detail->batch ?? '-' }}</div>
                                            <div>ED: {{ $detail->ed ?? '-' }}</div>
                                            <div>
                                                {{ $detail->utuhan ? $detail->satuan->nama_satuan ?? '-' : $detail->sediaan->nama_sediaan ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>

                        <!-- Aksi -->
                        <td class="px-4 py-3 flex justify-center space-x-2">
                            <button wire:click="$dispatch('edit-mutasi', { id: {{ $mutasi->id }} })"
                                class="p-2 bg-yellow-100 text-yellow-600 rounded-md hover:bg-yellow-200 transition"
                                title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm-2 2h.01" />
                                </svg>
                            </button>

                            <button wire:click="delete({{ $mutasi->id }})"
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
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">Belum ada mutasi stok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
