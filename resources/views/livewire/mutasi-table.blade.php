<div class="min-h-screen p-6 bg-gray-50">
    <!-- Header -->
    <div class="flex flex-col justify-between gap-4 mb-6 md:flex-row md:items-center">

        <!-- Bagian Kiri: Pencarian + Filter Tanggal -->
        <div class="flex flex-col w-full gap-2 md:flex-row md:items-center md:w-2/3">

            <!-- Input Pencarian -->
            <input type="text" wire:model.debounce.300ms="search" placeholder="Cari No Mutasi atau Keterangan"
                class="w-full px-4 py-2 transition border border-gray-300 rounded-md md:w-1/2 focus:ring-2 focus:ring-indigo-300 focus:outline-none">

            <!-- Tanggal Awal -->
            <input type="date" wire:model="start_date"
                class="w-full px-4 py-2 transition border border-gray-300 rounded-md md:w-1/4 focus:ring-2 focus:ring-indigo-300 focus:outline-none">

            <!-- Tanggal Akhir -->
            <input type="date" wire:model="end_date"
                class="w-full px-4 py-2 transition border border-gray-300 rounded-md md:w-1/4 focus:ring-2 focus:ring-indigo-300 focus:outline-none">
        </div>

        <!-- Bagian Kanan: Tombol Export -->
        <div class="flex flex-wrap gap-2 md:justify-end">
            <button wire:click="exportExcelDetailed"
                class="px-4 py-2 text-white transition bg-indigo-600 rounded-md hover:bg-indigo-700">
                Export (Detail)
            </button>

            <button wire:click="exportExcelSummary"
                class="px-4 py-2 text-white transition bg-gray-600 rounded-md hover:bg-gray-700">
                Export (Ringkas)
            </button>
        </div>
    </div>



    <h2 class="mb-4 text-2xl font-bold text-gray-800">📦 Daftar Mutasi Stok</h2>

    <!-- Tabel -->
    <div class="overflow-x-auto bg-white rounded-md shadow-md">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
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
                    <tr class="transition hover:bg-gray-50">
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
                                    <div class="p-3 border border-gray-200 rounded-lg shadow-sm bg-gray-50">
                                        <!-- Baris utama -->
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-gray-800">
                                                {{ $detail->obat->nama_obat ?? '-' }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-600">
                                                {{ $detail->qty }}
                                                {{ $detail->utuhan ? $detail->satuan->nama_satuan ?? '-' : $detail->sediaan->nama_sediaan ?? '-' }}
                                            </span>
                                            <span class="text-sm font-bold text-indigo-600">
                                                Rp {{ number_format($detail->harga, 0) }}
                                            </span>
                                        </div>

                                        <!-- Baris tambahan -->
                                        <div class="grid grid-cols-3 gap-2 mt-2 text-xs text-gray-500">
                                            <div>Batch: {{ $detail->batch ?? '-' }}</div>
                                            <div>ED: {{ \Carbon\Carbon::parse($detail->ed)->format('d-m-Y') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>

                        <!-- Aksi -->
                        <td class="flex justify-center px-4 py-3 space-x-2">
                            <button wire:click="delete({{ $mutasi->id }})"
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
                @empty
                    <tr>
                        <td colspan="7" class="py-4 text-center text-gray-500">Belum ada mutasi stok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Tambahkan pagination di sini -->
        <div class="flex justify-center mt-6 mb-4">
            {{ $mutasiList->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
