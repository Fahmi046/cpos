<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="p-4 bg-white rounded shadow mt-4">
        <h2 class="text-lg font-bold mb-2">Daftar Bentuk Sediaan</h2>
        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Kode</th>
                    <th class="border p-2">Nama</th>
                    <th class="border p-2">Deskripsi</th>
                    <th class="border p-2">Aktif</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sediaans as $item)
                    <tr>
                        <td class="border p-2">{{ $item->kode_sediaan }}</td>
                        <td class="border p-2">{{ $item->nama_sediaan }}</td>
                        <td class="border p-2">{{ $item->deskripsi }}</td>
                        <td class="border p-2">{{ $item->aktif ? 'Ya' : 'Tidak' }}</td>
                        <td class="border p-2">
                            <button wire:click="$dispatch('edit-sediaan', { id: {{ $item->id }} })"
                                class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</button>
                            <button wire:click="delete({{ $item->id }})"
                                class="px-2 py-1 bg-red-600 text-white rounded">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center p-2">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
