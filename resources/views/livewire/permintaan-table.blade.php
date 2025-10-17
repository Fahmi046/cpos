<div class="min-h-screen p-6 bg-gray-50">
    <!-- Header -->
    <div class="flex flex-col items-start justify-between gap-4 mb-6 md:flex-row md:items-center">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Cari No permintaan atau Keterangan"
            class="w-full px-4 py-2 transition border border-gray-300 rounded-md md:w-1/3 focus:ring-2 focus:ring-indigo-300 focus:outline-none">

        <div class="flex gap-2 mb-4">
            <button wire:click="exportExcelDetailed" class="px-3 py-2 text-white bg-indigo-600 rounded">Export
                (Detail)</button>
            <button wire:click="exportExcelSummary" class="px-3 py-2 text-white bg-gray-600 rounded">Export
                (Ringkas)</button>
        </div>
    </div>

    <h2 class="mb-4 text-2xl font-bold text-gray-800">📦 Daftar permintaan Stok</h2>

    <!-- Tabel -->
    <div class="overflow-x-auto bg-white rounded-md shadow-md">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-center">No</th>
                    <th class="px-4 py-3">No permintaan</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Outlet</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Detail permintaan</th>
                    <th class="px-4 py-3">Status</th> <!-- 👈 kolom baru -->
                    @unless (auth()->user()->role === 'outlet')
                        <th class="px-4 py-3 text-center">Aksi</th>
                    @endunless
                </tr>
            </thead>
            <tbody>
                @forelse($permintaanList as $permintaan)
                    <tr class="transition hover:bg-gray-50">
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
                                    <div class="p-3 border border-gray-200 rounded-lg shadow-sm bg-gray-50">
                                        <div class="flex items-center justify-between">
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
                                        <div class="grid grid-cols-3 gap-2 mt-2 text-xs text-gray-500">
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
                        <td class="flex justify-center px-4 py-3 space-x-2">
                            @unless (auth()->user()->role === 'outlet')
                                @if ($permintaan->status === 'pending')
                                    <!-- Tombol Hapus -->
                                    <button wire:click="delete({{ $permintaan->id }})"
                                        class="p-2 text-red-600 transition bg-red-100 rounded-md hover:bg-red-200"
                                        title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <!-- Tombol Tambah Mutasi -->
                                    <a href="{{ route('mutasi.create', ['permintaan_id' => $permintaan->id]) }}"
                                        class="p-2 text-blue-600 transition bg-blue-100 rounded-md hover:bg-blue-200"
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
                                        class="p-2 text-blue-600 transition bg-blue-100 rounded-md hover:bg-blue-200"
                                        title="Tambahkan Mutasi">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </a>
                                @endif
                            @endunless
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-4 text-center text-gray-500">Belum ada permintaan stok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Tambahkan pagination di sini -->
        <div class="flex justify-center mt-6 mb-4">
            {{ $permintaanList->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
