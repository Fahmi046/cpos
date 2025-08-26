<div class="p-4 bg-white rounded shadow">
    <form wire:submit.prevent="store" class="space-y-4" x-data x-init="$nextTick(() => $refs.nama_obat.focus())"
        x-on:focus-nama-obat.window="$refs.nama_obat.focus()">
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="kode_obat" class="block text-sm font-medium text-gray-700">Kode Obat</label>
                <input type="text" id="kode_obat" wire:model="kode_obat"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly />
            </div>
            <div>
                <label>Nama Obat</label>
                <input type="text" wire:model="nama_obat" class="w-full border rounded p-2" x-ref="nama_obat"
                    @keydown.enter.prevent="$refs.kategori.focus()">
                @error('nama_obat')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>Kategori</label>
                <input type="text" wire:model="searchKategori"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" placeholder="Cari kategori..."
                    x-ref="kategori" @keydown.arrow-down.prevent="$wire.incrementHighlight()"
                    @keydown.arrow-up.prevent="$wire.decrementHighlight()"
                    @keydown.enter.prevent="$wire.pilihHighlight(); $nextTick(() => $refs.bentuk_sediaan.focus())">

                {{-- Dropdown hasil pencarian --}}
                @if (!empty($kategoriList))
                    <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                        @foreach ($kategoriList as $index => $item)
                            <li wire:click="pilihKategori({{ $item->id }}, '{{ $item->nama_kategori }}')"
                                class="px-3 py-2 cursor-pointer
                    {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                {{ $item->nama_kategori }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Hidden field simpan ID kategori --}}
                <input type="hidden" name="kategori_id" value="{{ $kategori_id }}">
            </div>
            <div>
                <div>
                    <label>Bentuk Sediaan</label>
                    <input type="text" wire:model="searchsediaan"
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                        placeholder="Cari sediaan..." x-ref="bentuk_sediaan"
                        @keydown.arrow-down.prevent="$wire.incrementHighlightsediaan()"
                        @keydown.arrow-up.prevent="$wire.decrementHighlightsediaan()"
                        @keydown.enter.prevent="$wire.pilihHighlightsediaan(); $nextTick(() => $refs.komposisi.focus())">

                    {{-- Dropdown hasil pencarian --}}
                    @if (!empty($sediaanList))
                        <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                            @foreach ($sediaanList as $index => $item)
                                <li wire:click="pilihsediaan({{ $item->id }}, '{{ $item->nama_sediaan }}')"
                                    class="px-3 py-2 cursor-pointer
                    {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                    {{ $item->nama_sediaan }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Hidden field simpan ID kategori --}}
                    <input type="hidden" name="sediaan_id" value="{{ $sediaan_id }}">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>Komposisi</label>
                <input type="text" wire:model="searchkomposisi"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                    placeholder="Cari komposisi..." x-ref="komposisi"
                    @keydown.arrow-down.prevent="$wire.incrementHighlightkomposisi()"
                    @keydown.arrow-up.prevent="$wire.decrementHighlightkomposisi()"
                    @keydown.enter.prevent="$wire.pilihHighlightkomposisi(); $nextTick(() => $refs.satuan.focus())">

                {{-- Dropdown hasil pencarian --}}
                @if (!empty($komposisiList))
                    <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                        @foreach ($komposisiList as $index => $item)
                            <li wire:click="pilihkomposisi({{ $item->id }}, '{{ $item->nama_komposisi }}')"
                                class="px-3 py-2 cursor-pointer
                    {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                {{ $item->nama_komposisi }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Hidden field simpan ID kategori --}}
                <input type="hidden" name="komposisi_id" value="{{ $komposisi_id }}">
            </div>
            <div>
                <label>Satuan</label>
                <input type="text" wire:model="searchsatuan"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" placeholder="Cari satuan..."
                    x-ref="satuan" @keydown.arrow-down.prevent="$wire.incrementHighlightsatuan()"
                    @keydown.arrow-up.prevent="$wire.decrementHighlightsatuan()"
                    @keydown.enter.prevent="$wire.pilihHighlightsatuan(); $nextTick(() => $refs.isi_obat.focus())">

                {{-- Dropdown hasil pencarian --}}
                @if (!empty($satuanList))
                    <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                        @foreach ($satuanList as $index => $item)
                            <li wire:click="pilihsatuan({{ $item->id }}, '{{ $item->nama_satuan }}')"
                                class="px-3 py-2 cursor-pointer
                    {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                {{ $item->nama_satuan }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Hidden field simpan ID kategori --}}
                <input type="hidden" name="satuan_id" value="{{ $satuan_id }}">
            </div>
        </div>


        <div class="grid grid-cols-4 gap-4">
            <div>
                <label>Isi Obat</label>
                <input type="text" wire:model="isi_obat" class="w-full border rounded p-2"
                    placeholder="Contoh: 10 Tablet / Strip" x-ref="isi_obat"
                    @keydown.enter.prevent="$refs.dosis.focus()">
            </div>
            <div>
                <label>Dosis</label>
                <input type="text" wire:model="dosis" class="w-full border rounded p-2" placeholder="Contoh: 500mg"
                    x-ref="dosis" @keydown.enter.prevent="$refs.harga_beli.focus()">
            </div>
            <div>
                <label>Harga Beli</label>
                <input type="text" wire:model="harga_beli" step="0.01"
                    class="w-full border rounded p-2 format-rupiah" x-ref="harga_beli"
                    @keydown.enter.prevent="$refs.harga_jual.focus()">
            </div>
            <div>
                <label>Harga Jual + PPN 11%</label>
                <input type="text" wire:model="harga_jual" step="0.01"
                    class="w-full border rounded p-2 format-rupiah" x-ref="harga_jual"
                    @keydown.enter.prevent="$refs.pabrik.focus()">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>Pabrik</label>
                <input type="text" wire:model="searchpabrik"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring" placeholder="Cari pabrik..."
                    x-ref="pabrik" @keydown.arrow-down.prevent="$wire.incrementHighlightpabrik()"
                    @keydown.arrow-up.prevent="$wire.decrementHighlightpabrik()"
                    @keydown.enter.prevent="$wire.pilihHighlightpabrik(); $nextTick(() => $refs.kreditur.focus())">

                {{-- Dropdown hasil pencarian --}}
                @if (!empty($pabrikList))
                    <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                        @foreach ($pabrikList as $index => $item)
                            <li wire:click="pilihpabrik({{ $item->id }}, '{{ $item->nama_pabrik }}')"
                                class="px-3 py-2 cursor-pointer
                    {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                {{ $item->nama_pabrik }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Hidden field simpan ID kategori --}}
                <input type="hidden" name="pabrik_id" value="{{ $pabrik_id }}">
            </div>
            <div>
                <label>Kreditur</label>
                <input type="text" wire:model="searchkreditur"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                    placeholder="Cari kreditur..." x-ref="kreditur"
                    @keydown.arrow-down.prevent="$wire.incrementHighlightKreditur()"
                    @keydown.arrow-up.prevent="$wire.decrementHighlightKreditur()"
                    @keydown.enter.prevent="$wire.pilihHighlightKreditur(); $nextTick(() => $refs.utuh_satuan.focus())">

                {{-- Dropdown hasil pencarian --}}
                @if (!empty($krediturList))
                    <ul class="border bg-white rounded mt-1 max-h-40 overflow-y-auto">
                        @foreach ($krediturList as $index => $item)
                            <li wire:click="pilihkreditur({{ $item->id }}, '{{ $item->nama }}')"
                                class="px-3 py-2 cursor-pointer
                            {{ $highlightIndex === $index ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}">
                                {{ $item->nama }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Hidden field simpan ID kreditur --}}
                <input type="hidden" name="kreditur_id" value="{{ $kreditur_id }}">
            </div>
        </div>
        {{-- Checklist --}}
        <div class="grid grid-cols-5 gap-4">
            <label class="flex items-center space-x-2">
                <input type="checkbox" wire:model="utuh_satuan" value="1" wire:model="utuh_satuan"
                    {{ $utuh_satuan ? 'checked' : '' }} class="rounded border-gray-300" x-ref="utuh_satuan"
                    @keydown.enter.prevent="$refs.prekursor.focus()">
                <span>Utuh</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" wire:model="prekursor" value="1" wire:model="prekursor"
                    {{ $prekursor ? 'checked' : '' }} class="rounded border-gray-300" x-ref="prekursor"
                    @keydown.enter.prevent="$refs.psikotropika.focus()">
                <span>Prekursor</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" wire:model="psikotropika" class="rounded border-gray-300" value="1"
                    wire:model="psikotropika" {{ $psikotropika ? 'checked' : '' }} x-ref="psikotropika"
                    @keydown.enter.prevent="$refs.resep_active.focus()">
                <span>psikotropika</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" wire:model="resep_active" value="1" wire:model="resep_active"
                    {{ $resep_active ? 'checked' : '' }} class="rounded border-gray-300" x-ref="resep_active"
                    @keydown.enter.prevent="$refs.aktif.focus()">
                <span>Resep</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" value="1" wire:model="aktif" {{ $aktif ? 'checked' : '' }}
                    class="rounded border-gray-300" x-ref="aktif" @keydown.enter.prevent="$refs.submit.focus()">
                <span>Aktif</span>
            </label>
        </div>

        <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded" x-ref="submit">
            {{ $obat_id ? 'Update' : 'Simpan' }}
        </button>
    </form>
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
</div>
