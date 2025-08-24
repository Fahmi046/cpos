<div class="p-4 bg-white rounded shadow mt-4">
    <h2 class="text-lg font-bold mb-2">Daftar Obat</h2>

    @if (session()->has('message'))
        <div class="p-2 bg-green-100 text-green-700 mb-2">
            {{ session('message') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full border text-sm">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Kode</th>
                    <th class="border p-2">Nama</th>
                    <th class="border p-2">Kategori</th>
                    <th class="border p-2">Bentuk</th>
                    <th class="border p-2">Kandungan</th>
                    <th class="border p-2">Stok</th>
                    <th class="border p-2">Satuan</th>
                    <th class="border p-2">Pabrik</th>
                    <th class="border p-2">Harga Jual</th>
                    <th class="border p-2">Expired</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($obats as $obat)
                    <tr>
                        <td class="border p-2">{{ $obat->kode_obat }}</td>
                        <td class="border p-2">{{ $obat->nama_obat }}</td>
                        <td class="border p-2">{{ $obat->kategori->nama_kategori ?? '-' }}</td>
                        <td class="border p-2">{{ $obat->sediaan->nama_sediaan ?? '-' }}</td>
                        <td class="border p-2">{{ $obat->kandungan }}</td>
                        <td class="border p-2 text-center">{{ $obat->stok }}</td>
                        <td class="border p-2">{{ $obat->satuan->nama_satuan ?? '-' }}</td>
                        <td class="border p-2">{{ $obat->pabrik->nama_pabrik ?? '-' }}</td>
                        <td class="border p-2">{{ number_format($obat->harga_jual, 0, ',', '.') }}</td>
                        <td class="border p-2">
                            {{ $obat->tgl_expired ? \Carbon\Carbon::parse($obat->tgl_expired)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="border p-2 space-x-2">
                            <button wire:click="$dispatch('edit-obat', { id: {{ $obat->id }} })"
                                class="px-2 py-1 bg-yellow-500 text-white rounded">
                                Edit
                            </button>

                            <button wire:click="delete({{ $obat->id }})"
                                class="px-2 py-1 bg-red-500 text-white rounded">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center p-2">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
