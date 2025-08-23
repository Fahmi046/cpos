<div>
    {{-- Do your work, then step back. --}}
    <div class="mt-6 bg-white rounded-lg shadow">
        <h2 class="p-4 text-lg font-bold">Daftar Satuan Obat</h2>

        @if (session()->has('message'))
            <div class="px-4 py-2 mb-2 text-green-800 bg-green-200">
                {{ session('message') }}
            </div>
        @endif

        <table class="w-full border border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Kode</th>
                    <th class="p-2 border">Nama</th>
                    <th class="p-2 border">Deskripsi</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($satuans as $satuan)
                    <tr>
                        <td class="p-2 border">{{ $satuan->id }}</td>
                        <td class="p-2 border">{{ $satuan->kode_satuan }}</td>
                        <td class="p-2 border">{{ $satuan->nama_satuan }}</td>
                        <td class="p-2 border">{{ $satuan->deskripsi }}</td>
                        <td class="p-2 border">
                            @if ($satuan->aktif)
                                <span class="px-2 py-1 text-xs text-white bg-green-600 rounded">Aktif</span>
                            @else
                                <span class="px-2 py-1 text-xs text-white bg-red-600 rounded">Nonaktif</span>
                            @endif
                        </td>
                        <td class="p-2 border">
                            <button wire:click="$dispatch('editSatuan', { id: {{ $satuan->id }} })"
                                class="px-2 py-1 text-white bg-yellow-500 rounded">
                                Edit
                            </button>

                            <button wire:click="delete({{ $satuan->id }})"
                                class="px-2 py-1 text-white bg-red-600 rounded">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
