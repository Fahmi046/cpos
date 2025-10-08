<div class="mt-6 bg-white rounded-md shadow-md overflow-x-auto">
    <h2 class="text-lg font-semibold text-gray-800 mb-4 px-4">🏭 Pabrik</h2>

    <table class="w-full text-sm text-left border-collapse">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Kode</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Alamat</th>
                <th class="px-4 py-3">Telepon</th>
                <th class="px-4 py-3 text-center">Aktif</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pabriks as $pabrik)
                <tr class="hover:bg-gray-50 transition">
                    <!-- Kode -->
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $pabrik->kode_pabrik }}
                    </td>

                    <!-- Nama -->
                    <td class="px-4 py-3">
                        {{ $pabrik->nama_pabrik }}
                    </td>

                    <!-- Alamat -->
                    <td class="px-4 py-3 text-gray-600">
                        {{ $pabrik->alamat }}
                    </td>

                    <!-- Telepon -->
                    <td class="px-4 py-3 text-gray-600">
                        {{ $pabrik->telepon }}
                    </td>

                    <!-- Aktif -->
                    <td class="px-4 py-3 text-center">
                        <span
                            class="px-2 py-1 text-xs rounded-md
                            {{ $pabrik->aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $pabrik->aktif ? 'Ya' : 'Tidak' }}
                        </span>
                    </td>

                    <!-- Aksi -->
                    <td class="px-4 py-3 flex justify-center space-x-2">
                        <!-- Edit -->
                        <button wire:click="$dispatch('edit-pabrik', { id: {{ $pabrik->id }} })"
                            class="p-2 bg-yellow-100 text-yellow-600 rounded-md hover:bg-yellow-200 transition"
                            title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm-2 2h.01" />
                            </svg>
                        </button>

                        <!-- Hapus -->
                        <button wire:click="delete({{ $pabrik->id }})"
                            class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition" title="Hapus">
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
                    <td colspan="6" class="text-center py-4 text-gray-500">
                        Belum ada data pabrik
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-6 mb-4 flex justify-center">
        {{ $pabriks->links('vendor.pagination.custom') }}
    </div>
</div>
