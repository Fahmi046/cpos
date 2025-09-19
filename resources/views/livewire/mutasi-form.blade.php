<div x-data
    @keydown.window="
        if ($event.key === 'F10') {
            $event.preventDefault();
            $el.querySelector('form').dispatchEvent(new Event('submit', {cancelable: true, bubbles: true}));
        }
    "
    class="p-6 bg-white rounded-lg shadow">

    <h2 class="text-xl font-bold mb-4">Input Mutasi Stok</h2>

    {{-- Form --}}
    <form wire:submit.prevent="save" class="space-y-6" x-data x-init="$nextTick(() => $refs.tanggal.focus())"
        x-on:focus-tanggal.window="$refs.tanggal.focus()"
        x-on:focus-obat.window="
          $nextTick(() => {
              const el = $refs['nama_obat_' + $event.detail.index];
              if (el) el.focus();
          })
      "
        x-on:keydown.window.prevent.f10="document.getElementById('btnSave').click()">

        {{-- Header --}}
        <div class="grid grid-cols-2 gap-4">
            {{-- No Mutasi --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">No Mutasi</label>
                <input type="text" wire:model="no_mutasi" disabled
                    class="block w-full rounded-lg border-gray-300 bg-gray-100 text-sm p-2.5">
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" wire:model="tanggal" x-ref="tanggal"
                    @keydown.enter.prevent="$refs.searchoutlet.focus()"
                    class="block w-full rounded-lg border-gray-300 text-sm p-2.5 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        {{-- Outlet + Keterangan --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="relative">
                <label class="block mb-2 text-sm font-medium text-gray-700">Outlet</label>
                <input type="text" wire:model.debounce.300ms="searchoutlet" x-ref="searchoutlet"
                    @keydown.enter.prevent="$refs.keterangan.focus()"
                    wire:keydown.arrow-down.prevent="incrementHighlight"
                    wire:keydown.arrow-up.prevent="decrementHighlight"
                    wire:keydown.enter.prevent="selectHighlighted(); $dispatch('focus-start')"
                    placeholder="Cari outlet..."
                    class="block w-full rounded-lg border-gray-300 p-2.5 text-sm focus:ring-blue-500 focus:border-blue-500">

                {{-- Dropdown hasil pencarian --}}
                @if (!empty($outletResults))
                    <div
                        class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-56 overflow-y-auto">
                        <ul class="divide-y divide-gray-100">
                            @foreach ($outletResults as $i => $item)
                                <li wire:click="selectoutlet({{ $item['id'] }})"
                                    class="px-4 py-2 cursor-pointer text-sm
                                    {{ $highlightIndex === $i ? 'bg-blue-100 text-blue-700' : 'hover:bg-gray-50' }}">
                                    {{ $item['nama_outlet'] }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Keterangan</label>
                <input type="text" wire:model="keterangan" x-ref="keterangan"
                    @keydown.enter.prevent="document.getElementById('nama_obat_0')?.focus()"
                    class="block w-full rounded-lg border-gray-300 text-sm p-2.5 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        {{-- Detail --}}
        <div class="space-y-3">
            @forelse ($details as $i => $detail)
                <div class="grid grid-cols-11 gap-3 items-end border rounded-lg p-3 bg-gray-50">
                    {{-- Obat --}}
                    <div class="col-span-8 relative">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Obat</label>

                        <input type="text" placeholder="Cari obat..."
                            wire:model.debounce.300ms="obatSearch.{{ $i }}"
                            wire:keydown.arrow-down.prevent="highlightNextObat({{ $i }})"
                            wire:keydown.arrow-up.prevent="highlightPrevObat({{ $i }})"
                            wire:keydown.enter.prevent="selectHighlightedObat({{ $i }})"
                            id="nama_obat_{{ $i }}" x-ref="nama_obat_{{ $i }}"
                            @keydown.enter.prevent="$refs['qty_' + {{ $i }}]?.focus()"
                            class="w-full p-2 border rounded-lg">

                        @if (!empty($obatResults[$i]))
                            <ul class="absolute z-10 w-full bg-white border rounded shadow-md max-h-40 overflow-y-auto">
                                @foreach ($obatResults[$i] as $index => $ks)
                                    <li wire:click="selectObat({{ $i }}, {{ $ks->obat_id }})"
                                        class="px-2 py-1 cursor-pointer hover:bg-gray-200 {{ $highlightObatIndex[$i] === $index ? 'bg-gray-300' : '' }}">
                                        {{ $ks->nama_obat ?? '-' }} - Batch: {{ $ks->batch ?? '-' }} -
                                        ED: {{ $ks->ed ? \Carbon\Carbon::parse($ks->ed)->format('d-m-Y') : '-' }} -
                                        Stok: {{ $ks->stok ?? '-' }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Pabrik --}}
                    <div class="col-span-2">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Pabrik</label>
                        <input type="text" wire:model="details.{{ $i }}.pabrik"
                            class="w-full p-2 border rounded-lg text-center" disabled>
                    </div>

                    {{-- Utuhan --}}
                    <div class="col-span-1 flex flex-col items-center justify-center h-full">
                        <label class="flex flex-col items-center cursor-pointer">
                            <span class="mb-1 text-sm font-medium text-gray-500">Utuhan</span>
                            <div class="relative">
                                <input type="checkbox" wire:model="details.{{ $i }}.utuh"
                                    class="sr-only peer" disabled>
                                <div
                                    class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-green-500 transition-colors duration-300">
                                </div>
                                <div
                                    class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transform transition-transform duration-300">
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
                            class="w-full p-2 border rounded-lg text-center" disabled>
                    </div>

                    {{-- Isi Obat --}}
                    <div class="col-span-1">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Isi Obat</label>
                        <input type="number" min="0" wire:model="details.{{ $i }}.isi_obat"
                            class="w-full p-2 border rounded-lg text-center" disabled>
                    </div>

                    {{-- Harga --}}
                    <div class="col-span-2">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Harga</label>
                        <input type="text" x-ref="harga_{{ $i }}"
                            @keydown.enter.prevent="$refs['ed_' + {{ $i }}]?.focus()"
                            value="{{ number_format($detail['harga'] ?? 0, 0, ',', '.') }}"
                            class="w-full p-2 border rounded-lg text-right" placeholder="0" disabled>
                    </div>

                    {{-- ED --}}
                    <div class="col-span-2">
                        <label class="block mb-1 text-xs font-medium text-gray-700">ED</label>
                        <input type="date" wire:model="details.{{ $i }}.ed"
                            x-ref="ed_{{ $i }}"
                            @keydown.enter.prevent="$refs['batch_' + {{ $i }}]?.focus()"
                            class="w-full p-2 border rounded-lg" disabled>
                    </div>

                    {{-- Batch --}}
                    <div class="col-span-2">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Batch</label>
                        <input type="text" wire:model="details.{{ $i }}.batch"
                            x-ref="batch_{{ $i }}"
                            @keydown.enter.prevent="$refs['qty_' + {{ $i }}]?.focus()"
                            class="w-full p-2 border rounded-lg" disabled>
                    </div>

                    {{-- Stok --}}
                    <div class="col-span-1">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Stok</label>
                        <input type="number" min="0" wire:model.lazy="details.{{ $i }}.stok"
                            x-ref="stok_{{ $i }}"
                            @keydown.enter.prevent="$refs['qty_' + {{ $i }}]?.focus()"
                            class="w-full p-2 border rounded-lg text-center" disabled>
                    </div>

                    {{-- Qty --}}
                    <div class="col-span-1">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Qty</label>
                        <input type="number" min="0" wire:model.lazy="details.{{ $i }}.qty"
                            x-ref="qty_{{ $i }}"
                            @keydown.enter.prevent="
                                @if ($i + 1 < count($details)) $refs['nama_obat_' + {{ $i + 1 }}]?.focus();
                                @else
                                    $refs.addDetail?.focus(); @endif
                            "
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
        <button type="submit" id="btnSave" class="bg-green-600 text-white px-4 py-2 rounded">Simpan (F10)</button>
    </form>
</div>
