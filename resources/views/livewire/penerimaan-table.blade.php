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
                    <td class="border p-2 text-center">
                        <button wire:click="$dispatch('edit-penerimaan', { id: {{ $row->id }} })"
                            class="p-2 bg-yellow-100 text-yellow-600 rounded-full hover:bg-yellow-200 transition"
                            title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm-2 2h.01" />
                            </svg>
                        </button>
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
