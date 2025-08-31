<div class="p-4 bg-white rounded shadow">
    <div class="flex justify-between mb-4">
        <input type="text" wire:model.live="search" placeholder="Cari No SP atau Tanggal"
            class="border rounded px-2 py-1 w-1/3">
        <button wire:click="exportExcel" class="bg-green-600 text-white px-4 py-2 rounded">
            Export Excel
        </button>
    </div>

    <h2 class="text-lg font-bold mb-4">Daftar Pesanan</h2>
    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">No</th>
                <th class="border px-2 py-1">No SP</th>
                <th class="border px-2 py-1">Tanggal</th>
                <th class="border px-2 py-1">Detail Obat</th>
                <th class="border px-2 py-1">Total</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesananList as $key => $pesanan)
                <tr>
                    <td class="border px-2 py-1">{{ $key + 1 }}</td>
                    <td class="border px-2 py-1">{{ $pesanan->no_sp }}</td>
                    <td class="border px-2 py-1">{{ $pesanan->tanggal }}</td>
                    <td class="border px-2 py-1">
                        <ul class="list-disc list-inside">
                            @foreach ($pesanan->details as $detail)
                                <li>
                                    {{ $detail->obat->nama_obat ?? '-' }}
                                    ({{ $detail->qty }} x {{ number_format($detail->harga, 0) }})
                                    = <strong>{{ number_format($detail->jumlah, 0) }}</strong>
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="border px-2 py-1 font-bold">
                        {{ number_format($pesanan->details->sum('jumlah'), 0) }}
                    </td>
                    <td class="border px-2 py-1">
                        <button wire:click="edit({{ $pesanan->id }})"
                            class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                        <button wire:click="delete({{ $pesanan->id }})"
                            class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-2">Belum ada pesanan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
