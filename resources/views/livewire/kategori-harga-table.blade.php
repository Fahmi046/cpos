<div class="mt-6 overflow-x-auto bg-white rounded-md shadow-md">
    <h2 class="px-4 mb-4 text-lg font-semibold text-gray-800">💰 Kategori Harga</h2>

    <table class="w-full text-sm text-left border-collapse">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
            <tr>
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Persentase</th>
                <th class="px-4 py-3">Tipe</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kategoris as $index => $kategori)
                <tr class="transition hover:bg-gray-50">
                    <!-- Nomor -->
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $loop->iteration }}
                    </td>

                    <!-- Nama -->
                    <td class="px-4 py-3">
                        {{ $kategori->nama }}
                    </td>

                    <!-- Persentase -->
                    <td class="px-4 py-3 text-gray-600">
                        {{ $kategori->persentase }}%
                    </td>

                    <!-- Tipe -->
                    <td class="px-4 py-3 text-gray-700">
                        {{ strtoupper($kategori->tipe) }}
                    </td>

                    <!-- Aksi -->
                    <td class="flex justify-center px-4 py-3 space-x-2">
                        <!-- Tombol Edit -->
                        <button wire:click="edit({{ $kategori->id }})"
                            class="p-2 text-yellow-600 transition bg-yellow-100 rounded-md hover:bg-yellow-200"
                            title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm-2 2h.01" />
                            </svg>
                        </button>

                        <!-- Tombol Hapus -->
                        <button wire:click="delete({{ $kategori->id }})"
                            class="p-2 text-red-600 transition bg-red-100 rounded-md hover:bg-red-200" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500">
                        Belum ada kategori harga
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="flex justify-center mt-6 mb-4">
        {{ $kategoris->links('vendor.pagination.custom') }}
    </div>
</div>
