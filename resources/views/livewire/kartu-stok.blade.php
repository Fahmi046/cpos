<div class="p-6 bg-white shadow rounded-lg w-full" x-data x-init="$nextTick(() => $refs.obat.focus())"
    x-on:focus-start.window="$refs.start.focus()" x-on:focus-end.window="$refs.end.focus()">

    <h2 class="text-2xl font-bold mb-6 text-gray-700 flex items-center gap-2">
        📊 Kartu Stok
    </h2>

    {{-- Filter --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        {{-- Autocomplete Obat --}}
        <div class="relative">
            <label class="block mb-2 text-sm font-medium text-gray-700">Obat</label>
            <div class="relative">
                <input type="text" x-ref="obat" wire:model.live="searchObat"
                    wire:keydown.arrow-down.prevent="incrementHighlight"
                    wire:keydown.arrow-up.prevent="decrementHighlight"
                    wire:keydown.enter.prevent="selectHighlighted(); $dispatch('focus-start')"
                    placeholder="Ketik nama obat..."
                    class="w-full p-2.5 ps-10 text-sm border border-gray-300 rounded-lg bg-gray-50
                              focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.414l4.387
                                 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM14
                                 8a6 6 0 11-12 0 6 6 0 0112 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>

            {{-- Dropdown hasil pencarian --}}
            @if (!empty($obatResults))
                <div
                    class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg
                            max-h-56 overflow-y-auto">
                    <ul class="text-sm text-gray-700 divide-y divide-gray-100">
                        @foreach ($obatResults as $i => $item)
                            <li wire:click="selectObat({{ $item['id'] }})"
                                class="px-4 py-2 cursor-pointer
                                       {{ $highlightIndex === $i ? 'bg-blue-100 text-blue-700' : 'hover:bg-gray-100' }}">
                                {{ $item['nama_obat'] }}
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

    {{-- Tabel Ringkas --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full table-auto text-sm text-left text-gray-700">
            <thead class="text-xs uppercase bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Obat</th>
                    <th class="px-4 py-3">Batch / ED</th>
                    <th class="px-4 py-3">Jenis</th>
                    <th class="px-4 py-3 text-center">Masuk</th>
                    <th class="px-4 py-3 text-center">Keluar</th>
                    <th class="px-4 py-3 text-center">Saldo</th>
                    <th class="px-4 py-3">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $row)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                        <td class="px-4 py-3">{{ $row->obat?->nama_obat ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="font-medium">{{ $row->batch ?? '-' }}</span>
                                <span class="text-xs text-gray-500">
                                    ED: {{ $row->ed ? \Carbon\Carbon::parse($row->ed)->format('d-m-Y') : '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 capitalize">{{ $row->jenis ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-green-600 font-semibold">
                            {{ $row->jenis === 'masuk' ? $row->qty : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center text-red-600 font-semibold">
                            {{ $row->jenis === 'keluar' ? $row->qty : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-gray-900">
                            {{ $row->stok_akhir }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-sm">
                            {{ $row->keterangan ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6 mb-4 flex justify-center">
            {{ $riwayat->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
