<div class="max-w-7xl mx-auto p-4 bg-white rounded-lg shadow-md">
    <form wire:submit.prevent="save" class="space-y-6">

        {{-- 🔹 FORM PENERIMAAN --}}
        <div class="grid grid-cols-11 gap-4">

            <div class="col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">No Penerimaan</label>
                <input type="text" wire:model="no_penerimaan"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500 rounded"
                    readonly>
            </div>

            {{-- Pesanan --}}
            <div class="col-span-5 relative" x-data="{ open: @entangle('pesananList').defer.length > 0 }" x-init="$refs.no_sp.focus()">
                <label class="block mb-2 text-sm font-medium text-gray-900">Pesanan</label>

                <input type="text" placeholder="Cari No SP / Tanggal..." wire:model.debounce.300ms="search"
                    wire:keydown.arrow-down.prevent="highlightNext" wire:keydown.arrow-up.prevent="highlightPrev"
                    wire:keydown.enter.prevent="selectHighlighted"
                    @keydown.enter.prevent="$wire.selectHighlighted; $wire.showDropdown = false; $refs.tanggal.focus();"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                    @focus="open = true" @click.outside="open = false" x-ref="no_sp">

                @if (!empty($pesananList))
                    <ul class="absolute z-10 w-full bg-white border rounded-lg shadow-md mt-1 max-h-60 overflow-y-auto">
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
                <select wire:model="jenis_ppn" x-ref="jenis_ppn" @keydown.enter.prevent="$refs.no_faktur.focus()"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">-- Pilih --</option>
                    <option value="INCLUDE">INCLUDE</option>
                    <option value="EXCLUDE">EXCLUDE</option>
                    <option value="NON">NON</option>
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
                <select wire:model.lazy="jenis_bayar" x-ref="jenis_bayar" @keydown.enter.prevent="$refs.tenor.focus()"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">-- Pilih --</option>
                    <option value="CASH">CASH</option>
                    <option value="KREDIT">KREDIT</option>
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
                    @keydown.enter.prevent="$refs.jenis_bayar.focus()"
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

                        {{-- Utuhan --}}
                        <div class="col-span-1 flex flex-col items-center justify-center h-full">
                            {{-- Toggle Switch --}}
                            <label class="flex flex-col items-center cursor-pointer">
                                {{-- Label di atas --}}
                                <span
                                    class="mb-1 text-sm font-medium
                    text-gray-500 peer-checked:text-green-600">
                                    Utuhan
                                </span>

                                {{-- Input hidden --}}
                                <input type="checkbox" wire:model.live="details.{{ $i }}.utuh"
                                    wire:change="toggleUtuhSatuan({{ $i }})"
                                    x-ref="checkbox_{{ $i }}"
                                    @keydown.enter.prevent="$refs.addDetail?.focus()">

                            </label>
                        </div>


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
                            <input type="number" min="0" wire:model="details.{{ $i }}.harga"
                                class="w-full p-2 border rounded-lg text-center">
                        </div>

                        {{-- ED --}}
                        <div class="col-span-2">
                            <label class="block mb-1 text-xs font-medium text-gray-700">ED</label>
                            <input type="date" wire:model="details.{{ $i }}.ed"
                                class="w-full p-2 border rounded-lg">
                        </div>

                        {{-- Batch --}}
                        <div class="col-span-2">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Batch</label>
                            <input type="text" wire:model="details.{{ $i }}.batch"
                                class="w-full p-2 border rounded-lg">
                        </div>

                        {{-- Qty --}}
                        <div class="col-span-1">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Qty</label>
                            <input type="number" min="0" wire:model="details.{{ $i }}.qty"
                                class="w-full p-2 border rounded-lg text-center">
                        </div>

                        {{-- Disc 1 --}}
                        <div class="col-span-1">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Disc 1</label>
                            <input type="number" min="0" wire:model="details.{{ $i }}.disc1"
                                class="w-full p-2 border rounded-lg text-right">
                        </div>

                        {{-- Disc 2 --}}
                        <div class="col-span-1">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Disc 2</label>
                            <input type="number" min="0" wire:model="details.{{ $i }}.disc2"
                                class="w-full p-2 border rounded-lg text-right">
                        </div>

                        {{-- Disc 3 --}}
                        <div class="col-span-1">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Disc 3</label>
                            <input type="number" min="0" wire:model="details.{{ $i }}.disc3"
                                class="w-full p-2 border rounded-lg text-right">
                        </div>

                        {{-- jumlah --}}
                        <div class="col-span-2">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Jumlah</label>
                            <input type="number" min="0" wire:model="details.{{ $i }}.jumlah"
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
                <button type="button" wire:click="addDetail"
                    class="px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700">
                    + Tambah Baris
                </button>
            </div>
        </div>


        {{-- 🔹 SIMPAN --}}
        <div class="flex justify-end">
            <button type="submit"
                class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300">
                Simpan
            </button>
        </div>
    </form>
</div>
