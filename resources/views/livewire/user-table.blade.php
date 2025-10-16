<div class="p-6 bg-white rounded-lg shadow">
    <h2 class="mb-4 text-lg font-semibold text-gray-800">Daftar User</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-600 border border-gray-200 rounded-lg">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th class="w-32 px-4 py-3">Username</th>
                    <th class="w-56 px-4 py-3">Nama</th>
                    <th class="w-48 px-4 py-3">Email</th>
                    <th class="w-32 px-4 py-3">Role</th>
                    <th class="px-4 py-3 text-center w-28">Status</th>
                    <th class="w-32 px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $user->username }}</td>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ ucfirst($user->role) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($user->aktif)
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
                                <button wire:click="edit({{ $user->id }})"
                                    class="px-3 py-1 text-xs font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $user->id }})"
                                    onclick="return confirm('Yakin hapus user ini?')"
                                    class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-300">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 italic text-center text-gray-500">
                            Belum ada data user
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
