<div class="min-h-screen p-6 bg-gray-50">
    <h2 class="mb-6 text-2xl font-bold text-gray-800">Daftar Penerimaan</h2>

    {{-- Filter & Search --}}
    <div class="grid items-end grid-cols-1 gap-4 mb-6 md:grid-cols-4">

        {{-- Search --}}
        <div class="md:col-span-1">
            <label class="block mb-2 text-sm font-medium text-gray-700">Cari</label>
            <input type="text" wire:model.live="search" placeholder="Cari No Penerimaan / No Faktur..."
                class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-primary-500 text-sm p-2.5 shadow-sm">
        </div>



        {{-- Start Date --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Dari</label>
            <input type="date" x-ref="start" wire:model.live="start_date"
                class="w-full p-2.5 text-sm border border-gray-300 rounded-lg bg-gray-50
                       focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- End Date --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Sampai</label>
            <input type="date" x-ref="end" wire:model.live="end_date"
                class="w-full p-2.5 text-sm border border-gray-300 rounded-lg bg-gray-50
                       focus:ring-blue-500 focus:border-blue-500">
        </div>


        {{-- Tombol Export --}}
        <div>
            <button wire:click="exportExcel"
                class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white
                       bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 3a1 1 0 000 2h14a1 1 0 100-2H3zM3
                             7a1 1 0 000 2h14a1 1 0 100-2H3zM3
                             11a1 1 0 000 2h14a1 1 0 100-2H3zM3
                             15a1 1 0 000 2h14a1 1 0 100-2H3z" />
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Tabel Penerimaan --}}
    <div class="overflow-x-auto rounded-lg shadow-sm">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-600 uppercase bg-gray-100">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">No Faktur</th>
                    <th class="px-4 py-3">Kreditur</th>
                    <th class="px-4 py-3">Jatuh Tempo</th>
                    <th class="px-4 py-3">Detail Penerimaan</th>
                    <th class="px-4 py-3">Biaya</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($penerimaanList as $row)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $row->no_faktur }}</td>
                        <td class="px-4 py-3">{{ $row->kreditur->nama ?? '-' }}</td>
                        <td class="px-4 py-3">
                            {{ $row->jatuh_tempo ? \Carbon\Carbon::parse($row->jatuh_tempo)->format('d M Y') : '-' }}
                        </td>

                        {{-- Detail Obat --}}
                        <td class="px-4 py-3">
                            <ul class="space-y-2">
                                @foreach ($row->details as $detail)
                                    <li class="p-2 rounded bg-gray-50">
                                        <div class="font-medium text-gray-800">{{ $detail->obat->nama_obat ?? '-' }}
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Satuan:
                                            @if ($detail->utuhan)
                                                {{ $detail->satuan->nama_satuan ?? '-' }}
                                            @else
                                                {{ $detail->obat->sediaan->nama_sediaan ?? '-' }}
                                            @endif
                                            | Qty: {{ $detail->qty }}
                                            | Harga: Rp {{ number_format($detail->harga, 0, ',', '.') }} |
                                            Diskon:
                                            {{ $detail->disc1 == intval($detail->disc1) ? intval($detail->disc1) : $detail->disc1 }}%
                                            /
                                            {{ $detail->disc2 == intval($detail->disc2) ? intval($detail->disc2) : $detail->disc2 }}%
                                            /
                                            {{ $detail->disc3 == intval($detail->disc3) ? intval($detail->disc3) : $detail->disc3 }}%
                                            | Subtotal: Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </td>

                        {{-- Biaya --}}
                        <td class="px-4 py-3 text-sm">
                            <div>DPP: <span class="font-medium">Rp
                                    {{ number_format($row->dpp ?? 0, 0, ',', '.') }}</span></div>
                            <div>PPN: <span class="font-medium">Rp
                                    {{ number_format($row->ppn ?? 0, 0, ',', '.') }}</span></div>
                            <div>Total: <span class="font-bold text-primary-600">Rp
                                    {{ number_format($row->total ?? 0, 0, ',', '.') }}</span></div>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-3 space-x-2 text-center">
                            {{-- Tombol Cetak --}}
                            <a href="{{ route('penerimaan.print', $row->id) }}" target="_blank"
                                class="inline-flex items-center justify-center p-2 text-blue-600 transition bg-blue-100 rounded-full hover:bg-blue-200"
                                title="Cetak Faktur">
                                🖨️
                            </a>

                            {{-- Tombol Edit --}}
                            <button wire:click="$dispatch('edit-penerimaan', { id: @js($row->id) })"
                                class="p-2 text-yellow-600 transition bg-yellow-100 rounded-full hover:bg-yellow-200"
                                title="Edit">
                                ✏️
                            </button>

                            {{-- Tombol Hapus --}}
                            <button wire:click="delete({{ $row->id }})"
                                class="p-2 text-red-600 transition bg-red-100 rounded-full hover:bg-red-200"
                                title="Hapus">
                                🗑️
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-gray-500">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center mt-6 mb-4">
        {{ $penerimaanList->links('vendor.pagination.custom') }}
    </div>
</div>
