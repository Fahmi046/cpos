<div class="p-4 border rounded">
    <h2 class="text-lg font-bold mb-4">Daftar Outlet</h2>

    <table class="table-auto w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">Kode</th>
                <th class="border px-4 py-2">Nama</th>
                <th class="border px-4 py-2">Alamat</th>
                <th class="border px-4 py-2">Telepon</th>
                <th class="border px-4 py-2">PIC</th>
                <th class="border px-4 py-2">Status</th>
                <th class="border px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($outlets as $outlet)
                <tr>
                    <td class="border px-4 py-2">{{ $outlet->kode_outlet }}</td>
                    <td class="border px-4 py-2">{{ $outlet->nama_outlet }}</td>
                    <td class="border px-4 py-2">{{ $outlet->alamat }}</td>
                    <td class="border px-4 py-2">{{ $outlet->telepon }}</td>
                    <td class="border px-4 py-2">{{ $outlet->pic }}</td>
                    <td class="border px-4 py-2">{{ $outlet->aktif ? 'Aktif' : 'Tidak Aktif' }}</td>
                    <td class="border px-4 py-2 flex gap-2">
                        <button wire:click="edit({{ $outlet->id }})"
                            class="bg-yellow-400 px-2 py-1 rounded">Edit</button>
                        <button wire:click="delete({{ $outlet->id }})"
                            class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
