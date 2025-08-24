<div class="mt-4 bg-white rounded-lg shadow p-4">
    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">Kode</th>
                <th class="p-2 border">Nama</th>
                <th class="p-2 border">Alamat</th>
                <th class="p-2 border">Telepon</th>
                <th class="p-2 border">Aktif</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pabriks as $pabrik)
                <tr>
                    <td class="p-2 border">{{ $pabrik->kode_pabrik }}</td>
                    <td class="p-2 border">{{ $pabrik->nama_pabrik }}</td>
                    <td class="p-2 border">{{ $pabrik->alamat }}</td>
                    <td class="p-2 border">{{ $pabrik->telepon }}</td>
                    <td class="p-2 border">{{ $pabrik->aktif ? 'Ya' : 'Tidak' }}</td>
                    <td class="p-2 border">
                        <button wire:click="$dispatch('edit', {{ $pabrik->id }})"
                            class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                        <button wire:click="delete({{ $pabrik->id }})"
                            class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
