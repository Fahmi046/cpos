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
                <select wire:model="kategori_id" class="border rounded p-2 w-full" x-ref="kategori"
                    @keydown.enter.prevent="$refs.bentuk_sediaan.focus()">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($kategoriList as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Bentuk Sediaan</label>
                <select wire:model="sediaan_id" class="border rounded p-2 w-full" x-ref="bentuk_sediaan"
                    @keydown.enter.prevent="$refs.kandungan.focus()">
                    <option value="">-- Pilih Sediaan --</option>
                    @foreach ($sediaanList as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_sediaan }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label>Kandungan</label>
            <input type="text" wire:model="kandungan" class="w-full border rounded p-2" x-ref="kandungan"
                @keydown.enter.prevent="$refs.stok.focus()">
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label>Stok</label>
                <input type="number" wire:model="stok" class="w-full border rounded p-2" x-ref="stok"
                    @keydown.enter.prevent="$refs.satuan.focus()">
            </div>
            <div>
                <label>Satuan</label>
                <select wire:model="satuan_id" class="border rounded p-2 w-full" x-ref="satuan"
                    @keydown.enter.prevent="$refs.pabrik.focus()">
                    <option value="">-- Pilih Satuan --</option>
                    @foreach ($satuanList as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_satuan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Pabrik</label>
                <select wire:model="pabrik_id" class="border rounded p-2 w-full" x-ref="pabrik"
                    @keydown.enter.prevent="$refs.harga_beli.focus()">
                    <option value="">-- Pilih Pabrik --</option>
                    @foreach ($pabrikList as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_pabrik }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label>Harga Beli</label>
                <input type="number" wire:model="harga_beli" step="0.01" class="w-full border rounded p-2"
                    x-ref="harga_beli" @keydown.enter.prevent="$refs.harga_jual.focus()">
            </div>
            <div>
                <label>Harga Jual</label>
                <input type="number" wire:model="harga_jual" step="0.01" class="w-full border rounded p-2"
                    x-ref="harga_jual" @keydown.enter.prevent="$refs.tgl_expired.focus()">
            </div>
            <div>
                <label>Tanggal Expired</label>
                <input type="date" wire:model="tgl_expired" class="w-full border rounded p-2" x-ref="tgl_expired"
                    @keydown.enter.prevent="$refs.submit.focus()">
            </div>
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded" x-ref="submit">
            {{ $obat_id ? 'Update' : 'Simpan' }}
        </button>
    </form>
</div>
