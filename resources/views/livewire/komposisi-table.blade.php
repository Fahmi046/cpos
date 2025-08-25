<div class="p-4 bg-white rounded shadow">
    <h2 class="text-lg font-bold mb-2">Daftar Komposisi</h2>

    @if (session()->has('message'))
        <div class="p-2 bg-green-100 text-green-700 mb-2">
            {{ session('message') }}
        </div>
    @endif

    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Kode</th>
                <th class="border px-2 py-1">Nama</th>
                <th class="border px-2 py-1">Deskripsi</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($komposisis as $item)
                <tr>
                    <td class="border px-2 py-1">{{ $item->kode_komposisi }}</td>
                    <td class="border px-2 py-1">{{ $item->nama_komposisi }}</td>
                    <td class="border px-2 py-1">{{ $item->deskripsi }}</td>
                    <td class="border px-2 py-1">
                        <button wire:click="$dispatch('edit-komposisi', { id: {{ $item->id }} })"
                            class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                        <button wire:click="delete({{ $item->id }})"
                            class="px-2 py-1 bg-red-500 text-white rounded">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
