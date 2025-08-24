<div class="mt-4 bg-white p-4 rounded shadow">
    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">Kode</th>
                <th class="p-2 border">Nama</th>
                <th class="p-2 border">Deskripsi</th>
                <th class="p-2 border">Aktif</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($satuans as $satuan)
                <tr>
                    <td class="border p-2">{{ $satuan->kode_satuan }}</td>
                    <td class="border p-2">{{ $satuan->nama_satuan }}</td>
                    <td class="border p-2">{{ $satuan->deskripsi }}</td>
                    <td class="border p-2">{{ $satuan->aktif ? 'Ya' : 'Tidak' }}</td>
                    <td class="border p-2">
                        <button wire:click="$dispatch('edit-satuan', { id: {{ $satuan->id }} })"
                            class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</button>
                        <button wire:click="delete({{ $satuan->id }})"
                            class="px-2 py-1 bg-red-500 text-white rounded">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
