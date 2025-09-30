<div x-data
    @keydown.window="
    if ($event.key === 'F10') {
        $event.preventDefault();
        $el.querySelector('form').dispatchEvent(new Event('submit', {cancelable: true, bubbles: true}));
    }
">

    <div class="max-w-7xl mx-auto p-4 bg-white rounded-lg shadow-md">

        @if ($errors->any())
            <div class="p-3 mb-2 text-sm text-red-700 bg-red-100 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-6"
            x-on:focus-row.window="
        $nextTick(() => {
            const el = $refs['obat_id_' + $event.detail.index];
            if (el) el.focus();
        })
    ">

            {{-- 🔹 FORM PENERIMAAN --}}
            <div class="grid grid-cols-11 gap-4">

                <div class="col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">No Penerimaan</label>
                    <input type="text" wire:model="no_penerimaan"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500 rounded"
                        readonly>
                </div>

                {{-- Pesanan --}}
                <div class="col-span-5 relative" x-data="{ open: @entangle('pesananList').defer.length > 0 }" x-on:focus-nosp.window="$refs.no_sp.focus()"
                    x-init="$refs.no_sp.focus()">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Pesanan</label>

                    <input type="text" placeholder="Cari No SP / Tanggal..." wire:model.debounce.300ms="search"
                        wire:keydown.arrow-down.prevent="highlightNext" wire:keydown.arrow-up.prevent="highlightPrev"
                        wire:keydown.enter.prevent="selectHighlighted"
                        @keydown.enter.prevent="$wire.selectHighlighted; $wire.showDropdown = false; $refs.tanggal.focus();"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        @focus="open = true" @click.outside="open = false" x-ref="no_sp">

                    @if (!empty($pesananList))
                        <ul
                            class="absolute z-10 w-full bg-white border rounded-lg shadow-md mt-1 max-h-60 overflow-y-auto">
                            @foreach ($pesananList as $index => $pesanan)
                                <li wire:click="selectPesanan({{ $pesanan->id }})"
                                    class="px-4 py-2 cursor-pointer hover:bg-gray-200
                           {{ $highlightIndex === $index ? 'bg-gray-300' : '' }}">
                                    {{ $pesanan->no_sp }} - {{ $pesanan->tanggal }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>



                <div class="col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Terima</label>
                    <input type="date" wire:model.lazy="tanggal" x-ref="tanggal"
                        @keydown.enter.prevent="$refs.jenis_ppn.focus()"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500 rounded">
                </div>

                <div class="col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Jenis PPN</label>
                    <select wire:model.lazy="jenis_ppn" x-ref="jenis_ppn"
                        @keydown.enter.prevent="$refs.no_faktur.focus()"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Pilih --</option>
                        <option value="include">INCLUDE</option>
                        <option value="exclude">EXCLUDE</option>
                        <option value="non">NON</option>
                    </select>
                </div>

                <div class="col-span-3">
                    <label class="block mb-2 text-sm font-medium text-gray-900">No Faktur</label>
                    <input type="text" wire:model="no_faktur" x-ref="no_faktur"
                        @keydown.enter.prevent="$refs.jenis_bayar.focus()"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="col-span-3">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Kreditur</label>
                    <input type="text" wire:model="kreditur_nama"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500 rounded"
                        readonly>
                </div>

                <div class="col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Jenis Bayar</label>
                    <select wire:model.lazy="jenis_bayar" x-ref="jenis_bayar"
                        @keydown.enter.prevent="$refs.tenor.focus()"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Pilih --</option>
                        <option value="Cash">CASH</option>
                        <option value="Kredit">KREDIT</option>
                    </select>
                </div>

                <div class="col-span-1">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tenor (hari)</label>
                    <input type="number" wire:model="tenor" x-ref="tenor"
                        @keydown.enter.prevent="$refs.jatuh_tempo.focus()"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Jatuh Tempo</label>
                    <input type="date" wire:model="jatuh_tempo" x-ref="jatuh_tempo"
                        @keydown.enter.prevent="document.getElementById('nama_obat_0')?.focus()"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            {{-- 🔹 DETAIL PENERIMAAN --}}
            <div class="mt-6">
                <h3 class="mb-3 text-lg font-semibold text-gray-800">Detail Penerimaan</h3>

                <div class="space-y-3">
                    @forelse ($details as $i => $detail)
                        <div class="grid grid-cols-11 gap-3 items-end border rounded-lg p-3 bg-gray-50">
                            {{-- Obat --}}
                            <div class="col-span-3 relative">
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
                                    <ul
                                        class="absolute z-10 w-full bg-white border rounded shadow-md max-h-40 overflow-y-auto">
                                        @foreach ($obatResults[$i] as $index => $obat)
                                            <li wire:click="selectObat({{ $i }}, {{ $obat->id }})"
                                                class="px-2 py-1 cursor-pointer hover:bg-gray-200
                           {{ $highlightObatIndex[$i] === $index ? 'bg-gray-300' : '' }}">
                                                {{ $obat->nama_obat }}
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
                                <input type="number" min="0"
                                    wire:model="details.{{ $i }}.isi_obat"
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

                            {{-- Qty --}}
                            <div class="col-span-1">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Qty</label>
                                <input type="number" min="0"
                                    wire:model.lazy="details.{{ $i }}.qty"
                                    x-ref="qty_{{ $i }}"
                                    @keydown.enter.prevent="$refs['disc1_{{ $i }}']?.focus()"
                                    class="w-full p-2 border rounded-lg text-center">
                            </div>

                            {{-- Disc 1 --}}
                            <div class="col-span-1">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Disc 1</label>
                                <input type="number" min="0"
                                    wire:model.lazy="details.{{ $i }}.disc1"
                                    x-ref="disc1_{{ $i }}"
                                    @keydown.enter.prevent="$refs['disc2_{{ $i }}']?.focus()"
                                    class="w-full p-2 border rounded-lg text-right">
                            </div>

                            {{-- Disc 2 --}}
                            <div class="col-span-1">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Disc 2</label>
                                <input type="number" min="0"
                                    wire:model.lazy="details.{{ $i }}.disc2"
                                    x-ref="disc2_{{ $i }}"
                                    @keydown.enter.prevent="$refs['disc3_{{ $i }}']?.focus()"
                                    class="w-full p-2 border rounded-lg text-right">
                            </div>

                            {{-- Disc 3 --}}
                            <div class="col-span-1">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Disc 3</label>
                                <input type="number" min="0"
                                    wire:model.lazy="details.{{ $i }}.disc3"
                                    x-ref="disc3_{{ $i }}"
                                    @keydown.enter.prevent="
    @if ($i + 1 < count($details)) $refs['nama_obat_{{ $i + 1 }}']?.focus();
    @else
         $refs['addDetail']?.focus(); @endif
"
                                    class="w-full p-2 border rounded-lg text-right">
                            </div>


                            {{-- jumlah --}}
                            <div class="col-span-2">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Jumlah</label>
                                <input type="text"
                                    value="{{ number_format($detail['jumlah'] ?? 0, 0, ',', '.') }}" readonly
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
            </div>


            {{-- 🔹 SIMPAN --}}
            <div class="grid grid-cols-4 gap-4 items-end mt-6">

                {{-- DPP --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">DPP</label>
                    <input type="text" value="{{ number_format($dpp, 0, ',', '.') }}" readonly
                        class="w-full p-2 border rounded-lg text-right bg-gray-100 font-semibold" />
                </div>

                {{-- PPN --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">PPN (11%)</label>
                    <input type="text" value="{{ number_format($ppn, 0, ',', '.') }}" readonly
                        class="w-full p-2 border rounded-lg text-right bg-gray-100 font-semibold" />
                </div>

                {{-- TOTAL --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">TOTAL</label>
                    <input type="text" value="{{ number_format($total, 0, ',', '.') }}" readonly
                        class="w-full p-2 border rounded-lg text-right font-bold text-green-600 bg-gray-100" />
                </div>

                {{-- Tombol Simpan --}}
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">
                        Simpan (F10)
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
