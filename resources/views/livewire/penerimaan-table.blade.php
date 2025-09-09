<div class="p-4">
    <h2 class="text-lg font-bold mb-3">Daftar Penerimaan</h2>

    <input type="text" wire:model.debounce.300ms="search" placeholder="Cari No Penerimaan..."
        class="border p-2 rounded w-1/3 mb-3">

    <table class="w-full border-collapse border">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2">No Penerimaan</th>
                <th class="border p-2">Tanggal</th>
                <th class="border p-2">Pesanan</th>
                <th class="border p-2">Kreditur</th>
                <th class="border p-2">Jenis Bayar</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penerimaan as $row)
                <tr>
                    <td class="border p-2">{{ $row->no_penerimaan }}</td>
                    <td class="border p-2">{{ $row->tanggal }}</td>
                    <td class="border p-2">{{ $row->pesanan->no_sp ?? '-' }}</td>
                    <td class="border p-2">{{ $row->kreditur->nama ?? '-' }}</td>
                    <td class="border p-2">{{ $row->jenis_bayar }}</td>
                    <td class="border p-2">
                        <a href="{{ route('penerimaan.edit', $row->id) }}" class="text-blue-600">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center p-2">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $penerimaan->links() }}
    </div>
</div>
