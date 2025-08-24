<div class="p-4 bg-white rounded shadow">
    <form wire:submit.prevent="save" x-data x-init="$nextTick(() => $refs.nama_kategori.focus())"
        x-on:focus-nama-kategori.window="$refs.nama_kategori.focus()">
        <div class="mb-3">
            <label>Kode Kategori</label>
            <input type="text" wire:model="kode_kategori" class="w-full border p-2 rounded cursor-not-allowed" readonly>
            @error('kode_kategori')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label>Nama Kategori</label>
            <input type="text" wire:model="nama_kategori" class="w-full border p-2 rounded" x-ref="nama_kategori"
                @keydown.enter.prevent="$refs.deskripsi.focus()">
            @error('nama_kategori')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea wire:model="deskripsi" class="w-full border p-2 rounded" x-ref="deskripsi"
                @keydown.enter.prevent="$refs.checkbox.focus()"></textarea>
        </div>
        <div class="mb-3">
            <label><input type="checkbox" wire:model="aktif" x-ref="checkbox"
                    @keydown.enter.prevent="$refs.submit.focus()"> Aktif</label>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded" x-ref="submit">Simpan</button>
    </form>
</div>
