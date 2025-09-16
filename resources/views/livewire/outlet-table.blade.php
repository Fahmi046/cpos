<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Outlet</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-600 border border-gray-200 rounded-lg">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 w-32">Kode</th>
                    <th class="px-4 py-3 w-56">Nama Outlet</th>
                    <th class="px-4 py-3 w-40">Telepon</th>
                    <th class="px-4 py-3 w-40">PIC</th>
                    <th class="px-4 py-3 w-28 text-center">Status</th>
                    <th class="px-4 py-3 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($outlets as $outlet)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $outlet->kode_outlet }}</td>
                        <td class="px-4 py-3">{{ $outlet->nama_outlet }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $outlet->telepon }}</td>
                        <td class="px-4 py-3">{{ $outlet->pic }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($outlet->aktif)
                                <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-lg">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-lg">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <button wire:click="edit({{ $outlet->id }})"
                                    class="px-3 py-1 text-xs font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $outlet->id }})"
                                    onclick="return confirm('Yakin hapus outlet ini?')"
                                    class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-300">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500 italic">
                            Belum ada data outlet
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
