<div class="p-4">
    <div class="flex justify-between mb-4">
        <input type="text" wire:model.live="search" placeholder="Cari kreditur..." class="border rounded px-2 py-1" />
    </div>

    <table class="w-full border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 border">Nama</th>
                <th class="px-2 py-1 border">Alamat</th>
                <th class="px-2 py-1 border">Telepon</th>
                <th class="px-2 py-1 border">Email</th>
                <th class="px-2 py-1 border">Status</th>
                <th class="px-2 py-1 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($krediturs as $kreditur)
                <tr>
                    <td class="px-2 py-1 border">{{ $kreditur->nama }}</td>
                    <td class="px-2 py-1 border">{{ $kreditur->alamat }}</td>
                    <td class="px-2 py-1 border">{{ $kreditur->telepon }}</td>
                    <td class="px-2 py-1 border">{{ $kreditur->email }}</td>
                    <td class="px-2 py-1 border">
                        {{ $kreditur->aktif ? 'Aktif' : 'Nonaktif' }}
                    </td>
                    <td class="px-2 py-1 border">
                        <button wire:click="$dispatch('edit-kreditur', { id: {{ $kreditur->id }} })"
                            class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                        <button wire:click="delete({{ $kreditur->id }})"
                            class="bg-red-600 text-white px-2 py-1 rounded">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{ $krediturs->links() }}
    </div>
</div>
