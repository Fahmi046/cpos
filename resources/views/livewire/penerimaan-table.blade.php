<div class="p-6 bg-gray-50 min-h-screen">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Daftar Penerimaan</h2>

    {{-- Search --}}
    <div class="mb-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Cari No Penerimaan..."
            class="w-1/3 rounded-lg border-gray-300 focus:ring-2 focus:ring-primary-500 text-sm p-2.5 shadow-sm">
    </div>

    <div class="overflow-x-auto rounded-lg shadow-sm">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">No Penerimaan</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Kreditur</th>
                    <th class="px-4 py-3">Jatuh Tempo</th>
                    <th class="px-4 py-3">Detail Penerimaan</th>
                    <th class="px-4 py-3">Biaya</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($penerimaan as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $row->no_penerimaan }}</td>
                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">{{ $row->kreditur->nama ?? '-' }}</td>
                        <td class="px-4 py-3">
                            {{ $row->jatuh_tempo ? \Carbon\Carbon::parse($row->jatuh_tempo)->format('d M Y') : '-' }}
                        </td>
                        {{-- Detail Obat --}}
                        <td class="px-4 py-3">
                            <ul class="space-y-2">
                                @foreach ($row->details as $detail)
                                    <li class="p-2 bg-gray-50 rounded">
                                        <div class="font-medium text-gray-800">
                                            {{ $detail->obat->nama_obat ?? '-' }}
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Satuan:
                                            @if ($detail->utuhan)
                                                {{ $detail->satuan->nama_satuan ?? '-' }}
                                            @else
                                                {{ $detail->obat->sediaan->nama_sediaan ?? '-' }}
                                            @endif
                                            |
                                            Qty: {{ $detail->qty }}
                                            |
                                            Harga: Rp {{ number_format($detail->harga, 0, ',', '.') }}
                                            |
                                            <br>
                                            Diskon:
                                            {{ $detail->disc1 ?? 0 }}% /
                                            {{ $detail->disc2 ?? 0 }}% /
                                            {{ $detail->disc3 ?? 0 }}%
                                            |
                                            Subtotal: Rp
                                            {{ number_format(
                                                $detail->qty * $detail->harga -
                                                    (($detail->diskon1 + $detail->diskon2 + $detail->diskon3) / 100) * $detail->harga * $detail->qty,
                                                0,
                                                ',',
                                                '.',
                                            ) }}
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
                        <td class="px-4 py-3 text-center space-x-2">
                            {{-- Tombol Edit --}}
                            <button wire:click="$dispatch('edit-penerimaan', { id: @js($row->id) })"
                                class="p-2 bg-yellow-100 text-yellow-600 rounded-full hover:bg-yellow-200 transition"
                                title="Edit">
                                ✏️
                            </button>

                            {{-- Tombol Hapus --}}
                            <button wire:click="delete({{ $row->id }})"
                                class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition"
                                title="Hapus">
                                🗑️
                            </button>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $penerimaan->links() }}
    </div>
</div>
