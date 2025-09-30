<div class="mt-6 bg-white rounded-md shadow-md overflow-x-auto">
    <div class="flex justify-between items-center mb-4 px-4">
        <h2 class="text-lg font-semibold text-gray-800">💳 Kreditur</h2>
        <input type="text" wire:model.live="search" placeholder="Cari kreditur..."
            class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none" />
    </div>

    {{-- Flash message --}}
    @if (session()->has('message'))
        <div class="mx-4 mb-4 p-3 bg-green-100 text-green-700 text-sm rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <table class="w-full text-sm text-left border-collapse">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Alamat</th>
                <th class="px-4 py-3">Telepon</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($krediturs as $kreditur)
                <tr class="hover:bg-gray-50 transition">
                    <!-- Nama -->
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $kreditur->nama }}
                    </td>

                    <!-- Alamat -->
                    <td class="px-4 py-3 text-gray-600">
                        {{ $kreditur->alamat }}
                    </td>

                    <!-- Telepon -->
                    <td class="px-4 py-3">
                        {{ $kreditur->telepon }}
                    </td>

                    <!-- Email -->
                    <td class="px-4 py-3 text-gray-600">
                        {{ $kreditur->email }}
                    </td>

                    <!-- Status -->
                    <td class="px-4 py-3 text-center">
                        <span
                            class="px-2 py-1 text-xs rounded-md
                            {{ $kreditur->aktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $kreditur->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>

                    <!-- Aksi -->
                    <td class="px-4 py-3 flex justify-center space-x-2">
                        <button wire:click="$dispatch('edit-kreditur', { id: {{ $kreditur->id }} })"
                            class="p-2 bg-yellow-100 text-yellow-600 rounded-md hover:bg-yellow-200 transition"
                            title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm-2 2h.01" />
                            </svg>
                        </button>

                        <button wire:click="delete({{ $kreditur->id }})"
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
                    <td colspan="6" class="text-center py-4 text-gray-500">Belum ada kreditur</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-6 mb-4 flex justify-center">
        {{ $krediturs->links('vendor.pagination.custom') }}
    </div>
</div>
