<div class="bg-white rounded-md shadow p-4 mb-4">
    <h2 class="text-base font-semibold text-gray-700 mb-3">Master Obat</h2>

    <form wire:submit.prevent="store" x-data x-init="$nextTick(() => $refs.nama_obat.focus())" x-on:focus-nama-obat.window="$refs.nama_obat.focus()"
        class="space-y-3 text-sm">

        <!-- Baris 1: Kode & Nama -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- Nama Obat -->
            <div class="md:col-span-2">
                <label for="nama_obat" class="block mb-1">Nama Obat</label>
                <input type="text" id="nama_obat" wire:model="nama_obat" x-ref="nama_obat"
                    @keydown.enter.prevent="$refs.kategori.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                @error('nama_obat')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kode Obat -->
            <div>
                <label for="kode_obat" class="block mb-1">Kode</label>
                <input type="text" id="kode_obat" wire:model="kode_obat" readonly
                    class="w-full rounded border-gray-200 bg-gray-100 text-gray-600 px-2 py-1 cursor-not-allowed" />
                @error('kode_obat')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Baris 2: Kategori & Sediaan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block mb-1">Kategori</label>
                <input type="text" wire:model="searchKategori" x-ref="kategori"
                    @keydown.arrow-down.prevent="$wire.incrementHighlight()"
                    @keydown.arrow-up.prevent="$wire.decrementHighlight()"
                    @keydown.enter.prevent="$wire.pilihHighlight(); $nextTick(() => $refs.bentuk_sediaan.focus())"
                    placeholder="Cari kategori..."
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                {{-- Dropdown kategori --}}
                @if (!empty($kategoriList))
                    <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                        @foreach ($kategoriList as $index => $item)
                            <li wire:click="pilihKategori({{ $item->id }}, '{{ $item->nama_kategori }}')"
                                class="px-3 py-1 cursor-pointer text-sm {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                {{ $item->nama_kategori }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div>
                <label class="block mb-1">Bentuk Sediaan</label>
                <input type="text" wire:model="searchsediaan" x-ref="bentuk_sediaan"
                    @keydown.arrow-down.prevent="$wire.incrementHighlightsediaan()"
                    @keydown.arrow-up.prevent="$wire.decrementHighlightsediaan()"
                    @keydown.enter.prevent="$wire.pilihHighlightsediaan(); $nextTick(() => $refs.komposisi.focus())"
                    placeholder="Cari sediaan..."
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                {{-- Dropdown sediaan --}}
                @if (!empty($sediaanList))
                    <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                        @foreach ($sediaanList as $index => $item)
                            <li wire:click="pilihsediaan({{ $item->id }}, '{{ $item->nama_sediaan }}')"
                                class="px-3 py-1 cursor-pointer text-sm {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                {{ $item->nama_sediaan }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Baris 3: Komposisi & Satuan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block mb-1">Komposisi</label>
                <input type="text" wire:model="searchkomposisi" x-ref="komposisi"
                    @keydown.arrow-down.prevent="$wire.incrementHighlightkomposisi()"
                    @keydown.arrow-up.prevent="$wire.decrementHighlightkomposisi()"
                    @keydown.enter.prevent="$wire.pilihHighlightkomposisi(); $nextTick(() => $refs.satuan.focus())"
                    placeholder="Cari komposisi..."
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                {{-- Dropdown komposisi --}}
                @if (!empty($komposisiList))
                    <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                        @foreach ($komposisiList as $index => $item)
                            <li wire:click="pilihkomposisi({{ $item->id }}, '{{ $item->nama_komposisi }}')"
                                class="px-3 py-1 cursor-pointer text-sm {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                {{ $item->nama_komposisi }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div>
                <label class="block mb-1">Satuan</label>
                <input type="text" wire:model="searchsatuan" x-ref="satuan"
                    @keydown.arrow-down.prevent="$wire.incrementHighlightsatuan()"
                    @keydown.arrow-up.prevent="$wire.decrementHighlightsatuan()"
                    @keydown.enter.prevent="$wire.pilihHighlightsatuan(); $nextTick(() => $refs.isi_obat.focus())"
                    placeholder="Cari satuan..."
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                {{-- Dropdown satuan --}}
                @if (!empty($satuanList))
                    <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                        @foreach ($satuanList as $index => $item)
                            <li wire:click="pilihsatuan({{ $item->id }}, '{{ $item->nama_satuan }}')"
                                class="px-3 py-1 cursor-pointer text-sm {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                {{ $item->nama_satuan }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Baris 4: Isi, Dosis, Harga Beli, Harga Jual -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block mb-1">Isi Obat</label>
                <input type="text" wire:model="isi_obat" x-ref="isi_obat"
                    @keydown.enter.prevent="$refs.dosis.focus()" placeholder="10 Tablet / Strip"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
            </div>
            <div>
                <label class="block mb-1">Dosis</label>
                <input type="text" wire:model="dosis" x-ref="dosis"
                    @keydown.enter.prevent="$refs.harga_beli.focus()" placeholder="500mg"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
            </div>
            <div>
                <label class="block mb-1">Harga Beli</label>
                <input type="text" wire:model="harga_beli" x-ref="harga_beli"
                    @keydown.enter.prevent="$refs.harga_jual.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1 format-rupiah" />
            </div>
            <div>
                <label class="block mb-1">Harga Jual + PPN 11%</label>
                <input type="text" wire:model="harga_jual" x-ref="harga_jual"
                    @keydown.enter.prevent="$refs.stok_awal.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1 format-rupiah" />
            </div>
            <div>
                <label class="block mb-1">Stok Awal</label>
                <input type="text" wire:model="stok_awal" x-ref="stok_awal"
                    @keydown.enter.prevent="$refs.pabrik.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1 format-rupiah" />
            </div>
        </div>

        <!-- Baris 5: Pabrik & Kreditur -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block mb-1">Pabrik</label>
                <input type="text" wire:model="searchpabrik" x-ref="pabrik"
                    @keydown.arrow-down.prevent="$wire.incrementHighlightpabrik()"
                    @keydown.arrow-up.prevent="$wire.decrementHighlightpabrik()"
                    @keydown.enter.prevent="$wire.pilihHighlightpabrik(); $nextTick(() => $refs.kreditur.focus())"
                    placeholder="Cari pabrik..."
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                {{-- Dropdown pabrik --}}
                @if (!empty($pabrikList))
                    <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                        @foreach ($pabrikList as $index => $item)
                            <li wire:click="pilihpabrik({{ $item->id }}, '{{ $item->nama_pabrik }}')"
                                class="px-3 py-1 cursor-pointer text-sm {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                {{ $item->nama_pabrik }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div>
                <label class="block mb-1">Kreditur</label>
                <input type="text" wire:model="searchkreditur" x-ref="kreditur"
                    @keydown.arrow-down.prevent="$wire.incrementHighlightKreditur()"
                    @keydown.arrow-up.prevent="$wire.decrementHighlightKreditur()"
                    @keydown.enter.prevent="$wire.pilihHighlightKreditur(); $nextTick(() => $refs.utuh_satuan.focus())"
                    placeholder="Cari kreditur..."
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                {{-- Dropdown kreditur --}}
                @if (!empty($krediturList))
                    <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                        @foreach ($krediturList as $index => $item)
                            <li wire:click="pilihkreditur({{ $item->id }}, '{{ $item->nama }}')"
                                class="px-3 py-1 cursor-pointer text-sm {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                {{ $item->nama }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Baris 6: Checklist -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model="utuh_satuan" x-ref="utuh_satuan"
                    @keydown.enter.prevent="$refs.prekursor.focus()"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <span>Utuh</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model="prekursor" x-ref="prekursor"
                    @keydown.enter.prevent="$refs.psikotropika.focus()"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <span>Prekursor</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model="psikotropika" x-ref="psikotropika"
                    @keydown.enter.prevent="$refs.resep_active.focus()"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <span>Psikotropika</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model="resep_active" x-ref="resep_active"
                    @keydown.enter.prevent="$refs.aktif.focus()"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <span>Resep</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model="aktif" x-ref="aktif"
                    @keydown.enter.prevent="$refs.submit.focus()"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <span>Aktif</span>
            </label>
        </div>

        <!-- Tombol -->
        <div class="flex gap-2">
            <button type="submit" x-ref="submit"
                class="w-full md:w-auto px-4 py-1.5 rounded text-white bg-blue-600 hover:bg-blue-700 text-sm">
                {{ $obat_id ? 'Update' : 'Simpan' }}
            </button>
            <button type="button" wire:click="resetForm"
                class="w-full md:w-auto px-4 py-1.5 rounded text-gray-700 bg-gray-200 hover:bg-gray-300 text-sm">
                Batal
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.format-rupiah').forEach(function(el) {
            el.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                this.value = new Intl.NumberFormat('id-ID').format(value);
            });
        });
    });
</script>
