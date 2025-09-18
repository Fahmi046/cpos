<div class="p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4">Input Mutasi Stok</h2>

    {{-- Form --}}
    <form wire:submit.prevent="save">
        {{-- Header --}}
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label>No Mutasi</label>
                <input type="text" wire:model="no_mutasi" class="w-full border rounded p-2" readonly>
            </div>
            <div>
                <label>Tanggal</label>
                <input type="date" wire:model="tanggal" class="w-full border rounded p-2">
            </div>

            {{-- Input autocomplete Obat --}}
            <div class="relative">
                <label for="outlet" class="block mb-2 text-sm font-medium text-gray-700">outlet</label>
                <div class="relative">
                    <input type="text" x-ref="outlet" id="outlet" wire:model.live="searchoutlet"
                        wire:keydown.arrow-down.prevent="incrementHighlight"
                        wire:keydown.arrow-up.prevent="decrementHighlight"
                        wire:keydown.enter.prevent="selectHighlighted(); $dispatch('focus-start')"
                        placeholder="Ketik nama outlet..."
                        class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300
                       rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500">

                    {{-- Icon search --}}
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.414l4.387
                          4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM14
                          8a6 6 0 11-12 0 6 6 0 0112 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>

                {{-- Dropdown hasil pencarian --}}
                @if (!empty($outletResults))
                    <div
                        class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg
                       max-h-56 overflow-y-auto">
                        <ul class="text-sm text-gray-700 divide-y divide-gray-100">
                            @foreach ($outletResults as $i => $item)
                                <li wire:click="selectoutlet({{ $item['id'] }})"
                                    class="block w-full px-4 py-2 cursor-pointer
                            {{ $highlightIndex === $i ? 'bg-blue-100 text-blue-700' : 'hover:bg-gray-100' }}">
                                    {{ $item['nama_outlet'] }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div>
                <label>Keterangan</label>
                <input type="text" wire:model="keterangan" class="w-full border rounded p-2">
            </div>
        </div>

        {{-- Detail --}}

        <div class="space-y-3">
            @forelse ($details as $i => $detail)
                <div class="grid grid-cols-11 gap-3 items-end border rounded-lg p-3 bg-gray-50">
                    {{-- Obat --}}
                    <div class="col-span-4 relative">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Obat</label>

                        <input type="text" placeholder="Cari obat..."
                            wire:model.debounce.300ms="obatSearch.{{ $i }}"
                            wire:keydown.arrow-down.prevent="highlightNextObat({{ $i }})"
                            wire:keydown.arrow-up.prevent="highlightPrevObat({{ $i }})"
                            wire:keydown.enter.prevent="selectHighlightedObat({{ $i }})"
                            id="nama_obat_{{ $i }}" x-ref="nama_obat_{{ $i }}"
                            @keydown.enter.prevent="$refs['checkbox_{{ $i }}']?.focus()"
                            class="w-full p-2 border rounded-lg">

                        @if (!empty($obatResults[$i]))
                            <ul class="absolute z-10 w-full bg-white border rounded shadow-md max-h-40 overflow-y-auto">
                                @foreach ($obatResults[$i] as $index => $ks)
                                    <li wire:click="selectObat({{ $i }}, {{ $ks->obat_id }})"
                                        class="px-2 py-1 cursor-pointer hover:bg-gray-200 {{ $highlightObatIndex[$i] === $index ? 'bg-gray-300' : '' }}">
                                        {{ $ks->obat->nama_obat ?? '-' }} - Batch: {{ $ks->batch ?? '-' }} - ED:
                                        {{ $ks->ed ? \Carbon\Carbon::parse($ks->ed)->format('d-m-Y') : '-' }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Pabrik --}}
                    <div class="col-span-2">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Pabrik</label>
                        <input type="text" wire:model="details.{{ $i }}.pabrik"
                            class="w-full p-2 b order rounded-lg text-center">
                    </div>

                    <div class="col-span-1 flex flex-col items-center justify-center h-full">
                        <label class="flex flex-col items-center cursor-pointer">
                            <!-- Label di atas -->
                            <span class="mb-1 text-sm font-medium text-gray-500">Utuhan</span>

                            <!-- Toggle Switch -->
                            <div class="relative">
                                <input type="checkbox" wire:model.live="details.{{ $i }}.utuh"
                                    wire:change="toggleUtuhSatuan({{ $i }})"
                                    x-ref="checkbox_{{ $i }}" class="sr-only peer"
                                    @keydown.enter.prevent="$refs.harga_{{ $i }}?.focus()">

                                <div
                                    class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-green-500 transition-colors duration-300">
                                </div>
                                <div
                                    class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md
                        peer-checked:translate-x-5 transform transition-transform duration-300">
                                </div>
                            </div>
                        </label>
                    </div>


                    {{-- Hidden satuan_id --}}
                    <input type="hidden" wire:model="details.{{ $i }}.satuan_id">

                    {{-- Satuan --}}
                    <div class="col-span-1">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Satuan</label>
                        <input type="text" wire:model="details.{{ $i }}.satuan"
                            class="w-full p-2 border rounded-lg text-center">
                    </div>

                    {{-- isi_obat --}}
                    <div class="col-span-1">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Isi Obat</label>
                        <input type="number" min="0" wire:model="details.{{ $i }}.isi_obat"
                            class="w-full p-2 border rounded-lg text-center">
                    </div>

                    {{-- harga --}}
                    <div class="col-span-2">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Harga</label>
                        <input type="text" x-ref="harga_{{ $i }}"
                            @keydown.enter.prevent="$refs['ed_{{ $i }}']?.focus()"
                            wire:input.debounce.500ms="updateHarga({{ $i }}, $event.target.value)"
                            value="{{ number_format($detail['harga'] ?? 0, 0, ',', '.') }}"
                            wire:change="updateHarga({{ $i }}, $event.target.value)"
                            class="w-full p-2 border rounded-lg text-right" placeholder="0">
                    </div>

                    {{-- ED --}}
                    <div class="col-span-2">
                        <label class="block mb-1 text-xs font-medium text-gray-700">ED</label>
                        <input type="date" wire:model="details.{{ $i }}.ed"
                            x-ref="ed_{{ $i }}"
                            @keydown.enter.prevent="$refs['batch_{{ $i }}']?.focus()"
                            class="w-full p-2 border rounded-lg">
                    </div>

                    {{-- Batch --}}
                    <div class="col-span-2">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Batch</label>
                        <input type="text" wire:model="details.{{ $i }}.batch"
                            x-ref="batch_{{ $i }}"
                            @keydown.enter.prevent="$refs['qty_{{ $i }}']?.focus()"
                            class="w-full p-2 border rounded-lg">
                    </div>

                    {{-- stok --}}
                    <div class="col-span-1">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Stok</label>
                        <input type="number" min="0" wire:model.lazy="details.{{ $i }}.stok"
                            x-ref="stok_{{ $i }}"
                            @keydown.enter.prevent="$refs['disc1_{{ $i }}']?.focus()"
                            class="w-full p-2 border rounded-lg text-center">
                    </div>


                    {{-- Qty --}}
                    <div class="col-span-1">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Qty</label>
                        <input type="number" min="0" wire:model.lazy="details.{{ $i }}.qty"
                            x-ref="qty_{{ $i }}"
                            @keydown.enter.prevent="$refs['disc1_{{ $i }}']?.focus()"
                            class="w-full p-2 border rounded-lg text-center">
                    </div>

                    {{-- Hapus --}}
                    <div class="col-span-1 flex items-center justify-center mt-5">
                        <button type="button" wire:click="removeDetail({{ $i }})"
                            class="px-2 py-1 text-xs text-white bg-red-500 rounded-lg hover:bg-red-600">
                            ✕
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada baris detail</p>
            @endforelse
        </div>

        <div class="mt-3">
            <button type="button" wire:click="addDetail" x-ref="addDetail"
                class="bg-blue-500 text-white px-4 py-1 rounded mb-4">
                + Tambah Baris
            </button>
        </div>

        {{-- Tombol Simpan --}}
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan Mutasi</button>
    </form>
</div>
