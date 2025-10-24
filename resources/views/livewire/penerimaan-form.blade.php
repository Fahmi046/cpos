<div x-data
    @keydown.window="
        if ($event.key === 'F10') {
            $event.preventDefault();
            $el.querySelector('form').dispatchEvent(
                new Event('submit', { cancelable: true, bubbles: true })
            );
        }
    "
    x-on:focus-nama-obat.window="(e) => {
        // Tunggu Livewire render baris baru dulu
        setTimeout(() => {
            const nextInput = $refs[`nama_obat_${e.detail.index}`];
            if (nextInput) {
                nextInput.focus();
            }
        }, 300);
    }">


    <div class="p-4 mx-auto bg-white rounded-lg shadow-md max-w-7xl">

        @if ($errors->any())
            <div class="p-3 mb-2 text-sm text-red-700 bg-red-100 rounded-lg">
                <ul class="pl-5 list-disc">
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
                <div class="relative col-span-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Pesanan (No SP)</label>
                    <input type="text"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Cari No SP ..." wire:model.defer="no_sp"
                        wire:input="searchPesanan($event.target.value); resetPesananHighlight()"
                        @focus="$wire.showPesananDropdown = true"
                        @keydown.arrow-down.prevent="$wire.incrementPesananHighlight()"
                        @keydown.arrow-up.prevent="$wire.decrementPesananHighlight()"
                        @keydown.enter.prevent="
            $wire.selectHighlightedPesanan();
            $wire.showPesananDropdown = false;
            $refs['no_faktur']?.focus();
        "
                        x-ref="no_sp">

                    @if (!empty($pesananSearch) && ($showPesananDropdown ?? false))
                        <div
                            class="absolute p-2.5 bg-white border rounded-lg shadow-md mt-1 max-h-60 overflow-y-auto w-full z-50">
                            @foreach ($pesananSearch as $i => $pesanan)
                                <div class="px-3 py-1 text-sm cursor-pointer {{ ($highlightedPesananIndex ?? 0) === $i ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}"
                                    wire:click="selectPesanan({{ $pesanan->id }})"
                                    @click="$refs['no_faktur']?.focus()">
                                    <span class="font-semibold">{{ $pesanan->no_sp }}
                                </div>
                            @endforeach
                        </div>
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
                        @keydown.enter.prevent="$refs.kreditur_id.focus()"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="relative col-span-3">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Kreditur</label>
                    <input type="text"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Cari kreditur..." wire:model.defer="kreditur_nama"
                        wire:input="searchKreditur($event.target.value); resetKrediturHighlight()"
                        @focus="$wire.showKrediturDropdown = true"
                        @keydown.arrow-down.prevent="$wire.incrementKrediturHighlight()"
                        @keydown.arrow-up.prevent="$wire.decrementKrediturHighlight()"
                        @keydown.enter.prevent="
            $wire.selectHighlightedKreditur();
            $wire.showKrediturDropdown = false;
            $refs['jenis_bayar']?.focus();
        "
                        x-ref="kreditur_id">

                    @if (!empty($krediturSearch) && ($showKrediturDropdown ?? false))
                        <div
                            class="absolute p-2.5 bg-white border rounded-lg shadow-md mt-1 max-h-60 overflow-y-auto w-full">
                            @foreach ($krediturSearch as $i => $kreditur)
                                <div class="px-3 py-1 text-sm cursor-pointer {{ ($highlightedKrediturIndex ?? 0) === $i ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}"
                                    wire:click="selectKreditur({{ $kreditur->id }})"
                                    @click="$refs['jenis_bayar']?.focus()">
                                    {{ $kreditur->nama }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>



                <div class="col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Jenis Bayar</label>
                    <select wire:model.lazy="jenis_bayar" x-ref="jenis_bayar"
                        @keydown.enter.prevent="$refs.tenor.focus()"
                        class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Pilih --</option>
                        <option value="Cash">CASH</option>
                        <option value="Kredit">KREDIT</option>
                        <option value="Konsinyasi">KONSINYASI</option>
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
                        <div class="grid items-end grid-cols-11 gap-3 p-3 border rounded-lg bg-gray-50">
                            {{-- Obat --}}
                            <div class="relative col-span-3">
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
                                        class="absolute z-10 w-full overflow-y-auto bg-white border rounded shadow-md max-h-40">
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
                                    class="w-full p-2 text-center rounded-lg b order">
                            </div>

                            <div class="flex flex-col items-center justify-center h-full col-span-1">
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
                                            class="h-6 transition-colors duration-300 bg-gray-300 rounded-full w-11 peer-checked:bg-green-500">
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
                                    class="w-full p-2 text-center border rounded-lg">
                            </div>

                            {{-- isi_obat --}}
                            <div class="col-span-1">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Isi Obat</label>
                                <input type="number" min="0"
                                    wire:model="details.{{ $i }}.isi_obat"
                                    class="w-full p-2 text-center border rounded-lg">
                            </div>

                            {{-- harga --}}
                            <div class="col-span-2">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Harga</label>
                                <input type="number" step="0.01" min="0"
                                    x-ref="harga_{{ $i }}"
                                    @keydown.enter.prevent="$refs['ed_{{ $i }}']?.focus()"
                                    wire:model.lazy="details.{{ $i }}.harga"
                                    class="w-full p-2 text-right border rounded-lg" placeholder="0.00">
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
                                    class="w-full p-2 text-center border rounded-lg">
                            </div>

                            {{-- Disc 1 --}}
                            <div class="col-span-1">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Disc 1</label>
                                <input type="text"
                                    x-on:input="
            $el.value = $el.value.replace(',', '.');
            $wire.set('details.{{ $i }}.disc1', parseFloat($el.value) || 0);
        "
                                    x-ref="disc1_{{ $i }}"
                                    @keydown.enter.prevent="$refs['disc2_{{ $i }}']?.focus()"
                                    class="w-full p-2 text-right border rounded-lg" placeholder="0,0">
                            </div>

                            {{-- Disc 2 --}}
                            <div class="col-span-1">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Disc 2</label>
                                <input type="text"
                                    x-on:input="
            $el.value = $el.value.replace(',', '.');
            $wire.set('details.{{ $i }}.disc2', parseFloat($el.value) || 0);
        "
                                    x-ref="disc2_{{ $i }}"
                                    @keydown.enter.prevent="$refs['disc3_{{ $i }}']?.focus()"
                                    class="w-full p-2 text-right border rounded-lg" placeholder="0,0">
                            </div>

                            {{-- Disc 3 --}}
                            <div class="col-span-1">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Disc 3</label>
                                <input type="text"
                                    x-on:input="
            $el.value = $el.value.replace(',', '.');
            $wire.set('details.{{ $i }}.disc3', parseFloat($el.value) || 0);
        "
                                    x-ref="disc3_{{ $i }}"
                                    @keydown.enter.prevent="
            @if ($i + 1 < count($details)) $refs['nama_obat_{{ $i + 1 }}']?.focus();
            @else
                $refs['addDetail']?.focus(); @endif
        "
                                    class="w-full p-2 text-right border rounded-lg" placeholder="0,0">
                            </div>



                            {{-- jumlah --}}
                            <div class="col-span-2">
                                <label class="block mb-1 text-xs font-medium text-gray-700">Jumlah</label>
                                <input type="text"
                                    value="{{ number_format($detail['jumlah'] ?? 0, 0, ',', '.') }}" readonly
                                    class="w-full p-2 text-center border rounded-lg">

                            </div>
                            {{-- Hapus --}}
                            <div class="flex items-center justify-center col-span-1 mt-5">
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
                        class="px-4 py-1 mb-4 text-white bg-blue-500 rounded">
                        + Tambah Baris
                    </button>
                </div>
            </div>


            {{-- 🔹 SIMPAN --}}
            <div class="grid items-end grid-cols-6 gap-4 mt-6">

                {{-- SUBTOTAL --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                    <input type="text" value="{{ number_format($subtotal, 0, ',', '.') }}" readonly
                        class="w-full p-2 font-semibold text-right bg-gray-100 border rounded-lg" />
                </div>

                {{-- DISKON --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">diskon</label>
                    <input type="text" value="{{ number_format($diskon, 0, ',', '.') }}" readonly
                        class="w-full p-2 font-semibold text-right bg-gray-100 border rounded-lg" />
                </div>

                {{-- DPP --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">DPP</label>
                    <input type="text" value="{{ number_format($dpp, 0, ',', '.') }}" readonly
                        class="w-full p-2 font-semibold text-right bg-gray-100 border rounded-lg" />
                </div>

                {{-- PPN --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">PPN (11%)</label>
                    <input type="text" value="{{ number_format($ppn, 0, ',', '.') }}" readonly
                        class="w-full p-2 font-semibold text-right bg-gray-100 border rounded-lg" />
                </div>

                {{-- TOTAL --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">TOTAL</label>
                    <input type="text" value="{{ number_format($total, 0, ',', '.') }}" readonly
                        class="w-full p-2 font-bold text-right text-green-600 bg-gray-100 border rounded-lg" />
                </div>

                {{-- Tombol Simpan --}}
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 text-white bg-green-600 rounded-lg shadow hover:bg-green-700">
                        Simpan (F10)
                    </button>
                </div>
            </div>


        </form>
    </div>
</div>
