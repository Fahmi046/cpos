<div class="w-full p-6 bg-white rounded-lg shadow" x-data x-init="$nextTick(() => $refs.outlet.focus())"
    x-on:focus-start.window="$refs.start.focus()" x-on:focus-end.window="$refs.end.focus()">

    <h2 class="flex items-center gap-2 mb-6 text-2xl font-bold text-gray-700">
        🏪 Stok Outlet
    </h2>

    {{-- Filter --}}
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">

        {{-- Autocomplete Outlet --}}
        <div class="relative">
            <label class="block mb-2 text-sm font-medium text-gray-700">Outlet</label>
            <div class="relative">
                <input type="text" x-ref="outlet" wire:model.live="searchOutlet"
                    wire:keydown.arrow-down.prevent="incrementHighlight"
                    wire:keydown.arrow-up.prevent="decrementHighlight"
                    wire:keydown.enter.prevent="selectHighlighted(); $dispatch('focus-start')"
                    placeholder="Ketik nama outlet..."
                    class="w-full p-2.5 ps-10 text-sm border border-gray-300 rounded-lg bg-gray-50
                              focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-3">
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.414l4.387
                                 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM14
                                 8a6 6 0 11-12 0 6 6 0 0112 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>

            {{-- Dropdown hasil pencarian --}}
            @if (!empty($outletResults))
                <div
                    class="absolute z-20 w-full mt-1 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg max-h-56">
                    <ul class="text-sm text-gray-700 divide-y divide-gray-100">
                        @foreach ($outletResults as $i => $item)
                            <li wire:click="selectOutlet({{ $item['id'] }})"
                                class="px-4 py-2 cursor-pointer
                                       {{ $highlightIndex === $i ? 'bg-blue-100 text-blue-700' : 'hover:bg-gray-100' }}">
                                {{ $item['nama_outlet'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Start Date --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Dari</label>
            <input type="date" x-ref="start" wire:model.live="start_date"
                class="w-full p-2.5 text-sm border border-gray-300 rounded-lg bg-gray-50
                          focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- End Date --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Sampai</label>
            <input type="date" x-ref="end" wire:model.live="end_date"
                class="w-full p-2.5 text-sm border border-gray-300 rounded-lg bg-gray-50
                          focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Tombol Export --}}
        <div class="flex items-end">
            <button wire:click="exportExcel"
                class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white
                           bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 3a1 1 0 000 2h14a1 1 0 100-2H3zM3
                             7a1 1 0 000 2h14a1 1 0 100-2H3zM3
                             11a1 1 0 000 2h14a1 1 0 100-2H3zM3
                             15a1 1 0 000 2h14a1 1 0 100-2H3z" />
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Tabel Stok Outlet --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full text-sm text-left text-gray-700 table-auto">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Outlet</th>
                    <th class="px-4 py-3">Obat</th>
                    <th class="px-4 py-3 text-center">Stok Awal</th>
                    <th class="px-4 py-3 text-center">Masuk</th>
                    <th class="px-4 py-3 text-center">Keluar</th>
                    <th class="px-4 py-3 text-center">Saldo Akhir</th>
                    <th class="px-4 py-3">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stokOutlet as $row)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                        <td class="px-4 py-3">{{ $row->outlet?->nama_outlet ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $row->obat?->nama_obat ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ number_format($row->stok_awal ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-center text-green-600">
                            {{ number_format($row->masuk ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-center text-red-600">
                            {{ number_format($row->keluar ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 font-bold text-center text-gray-900">
                            {{ number_format($row->stok_akhir ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $row->keterangan ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="flex justify-center mt-6 mb-4">
            {{ $stokOutlet->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
