<div class="p-6 bg-white rounded-lg shadow-md">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <input type="text" wire:model.live="search" placeholder="Cari No SP atau Tanggal"
            class="border border-gray-300 rounded-lg px-3 py-2 w-1/3 focus:ring focus:ring-indigo-200 focus:outline-none">

        <button wire:click="exportExcel"
            class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7h12M8 12h12M8 17h12M3 7h.01M3 12h.01M3 17h.01" />
            </svg>
            Export
        </button>
    </div>

    <h2 class="text-xl font-semibold mb-4 text-gray-700">📋 Daftar Pesanan</h2>

    <!-- Tabel -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-3 py-2 text-center">No</th>
                    <th class="px-3 py-2">No SP</th>
                    <th class="px-3 py-2">Tanggal</th>
                    <th class="px-3 py-2">Nama Obat</th>
                    <th class="px-3 py-2">Pabrik</th>
                    <th class="px-3 py-2">Kreditur</th>
                    <th class="px-3 py-2">Satuan</th>
                    <th class="px-3 py-2 text-center">Qty</th>
                    <th class="px-3 py-2 text-right">Harga</th>
                    <th class="px-3 py-2 text-right">Jumlah</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesananList as $key => $pesanan)
                    @foreach ($pesanan->details as $detail)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-3 py-2 text-center">{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                            <td class="px-3 py-2">{{ $pesanan->no_sp }}</td>
                            <td class="px-3 py-2">{{ $pesanan->tanggal }}</td>
                            <td class="px-3 py-2">{{ $detail->obat->nama_obat ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $detail->pabrik->nama_pabrik ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $detail->kreditur->nama ?? '-' }}</td>
                            <td class="px-3 py-2">
                                @if ($detail->utuhan)
                                    {{ $detail->satuan->nama_satuan ?? '-' }}
                                @else
                                    {{ $detail->sediaan->nama_sediaan ?? '-' }}
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">{{ $detail->qty }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($detail->harga, 0) }}</td>
                            <td class="px-3 py-2 text-right font-semibold text-gray-900">
                                {{ number_format($detail->jumlah, 0) }}
                            </td>
                            <td class="px-3 py-2 flex justify-center space-x-2">
                                <!-- Edit -->
                                <button wire:click="$dispatch('edit-pesanan', { id: {{ $pesanan->id }} })"
                                    class="p-2 bg-yellow-100 text-yellow-600 rounded-full hover:bg-yellow-200 transition"
                                    title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm-2 2h.01" />
                                    </svg>
                                </button>

                                <!-- Hapus -->
                                <button wire:click="delete({{ $pesanan->id }})"
                                    class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition"
                                    title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <!-- Cetak -->
                                <a href="{{ route('pesanan.print', $pesanan->id) }}" target="_blank"
                                    class="p-2 bg-indigo-100 text-indigo-600 rounded-full hover:bg-indigo-200 transition"
                                    title="Cetak">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V2H7v7H3v13h18V9h-4zM9 22v-4h6v4H9z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-3 text-gray-500">Belum ada pesanan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
