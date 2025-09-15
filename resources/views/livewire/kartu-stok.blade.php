<div class="p-6 bg-white shadow rounded-lg w-full" x-data x-init="$nextTick(() => $refs.obat.focus())"
    x-on:focus-start.window="$refs.start.focus()" x-on:focus-end.window="$refs.end.focus()">

    <h2 class="text-2xl font-bold mb-6 text-gray-700 flex items-center gap-2">
        📊 Kartu Stok
    </h2>

    {{-- Filter --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        {{-- Input autocomplete Obat --}}
        <div class="relative">
            <label for="obat" class="block mb-2 text-sm font-medium text-gray-700">Obat</label>
            <input type="text" x-ref="obat" id="obat" wire:model.live="searchObat"
                wire:keydown.arrow-down.prevent="incrementHighlight" wire:keydown.arrow-up.prevent="decrementHighlight"
                wire:keydown.enter.prevent="selectHighlighted(); $dispatch('focus-start')"
                placeholder="Ketik nama obat..."
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                          focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">

            @if (!empty($obatResults))
                <ul
                    class="absolute z-10 bg-white border border-gray-300 w-full rounded-lg mt-1 max-h-48 overflow-y-auto shadow-md">
                    @foreach ($obatResults as $i => $item)
                        <li wire:click="selectObat({{ $item['id'] }})"
                            class="px-3 py-2 cursor-pointer
                            {{ $highlightIndex === $i ? 'bg-blue-100 text-blue-700' : 'hover:bg-gray-100' }}">
                            {{ $item['nama_obat'] }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Start Date --}}
        <div>
            <label for="start_date" class="block mb-2 text-sm font-medium text-gray-700">Dari</label>
            <input type="date" x-ref="start" id="start_date" wire:model.live="start_date"
                wire:keydown.enter.prevent="$dispatch('focus-end')"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                          focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
        </div>

        {{-- End Date --}}
        <div>
            <label for="end_date" class="block mb-2 text-sm font-medium text-gray-700">Sampai</label>
            <input type="date" x-ref="end" id="end_date" wire:model.live="end_date"
                wire:keydown.enter.prevent="$wire.filter()"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                          focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
        </div>
    </div>


    {{-- Tabel --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full table-auto text-sm text-left text-gray-700">
            <thead class="text-xs uppercase bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Obat</th>
                    <th class="px-4 py-3">Satuan</th>
                    <th class="px-4 py-3">Pabrik</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3 text-right">Harga</th>
                    <th class="px-4 py-3 text-center">Masuk</th>
                    <th class="px-4 py-3 text-center">Keluar</th>
                    <th class="px-4 py-3 text-center">Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $row)
                    <tr class="border-b hover:bg-gray-50">
                        {{-- Tanggal --}}
                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}
                        </td>

                        {{-- Detail Obat --}}
                        <td class="px-4 py-3">{{ $row->obat?->nama_obat ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $row->penerimaanDetail?->satuan?->nama_satuan ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $row->penerimaanDetail?->pabrik?->nama_pabrik ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $row->obat?->kategori?->nama_kategori ?? '-' }}</td>

                        {{-- Harga --}}
                        <td class="px-4 py-3 text-right">
                            {{ $row->penerimaanDetail?->harga ? 'Rp ' . number_format($row->penerimaanDetail->harga, 0, ',', '.') : '-' }}
                        </td>

                        {{-- Masuk & Keluar --}}
                        <td class="px-4 py-3 text-center text-green-600 font-semibold">
                            {{ $row->qty > 0 ? $row->qty : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center text-red-600 font-semibold">
                            {{ $row->qty < 0 ? abs($row->qty) : '-' }}
                        </td>

                        {{-- Stok Akhir --}}
                        <td class="px-4 py-3 text-center font-bold text-gray-900">
                            {{ $row->stok_akhir }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-4 py-6 text-center text-gray-500">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
