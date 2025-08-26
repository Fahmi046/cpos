<div class="p-4 bg-white rounded shadow mb-4">
    <form wire:submit.prevent="store" class="space-y-4" x-data x-init="$nextTick(() => $refs.nama_komposisi.focus())"
        x-on:focus-nama-komposisi.window="$refs.nama_komposisi.focus()">
        <div>
            <label>Kode Komposisi</label>
            <input type="text" wire:model="kode_komposisi" class="w-full border rounded p-2 cursor-not-allowed"
                readonly>
            @error('kode_komposisi')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label>Nama Komposisi</label>
            <input type="text" wire:model="nama_komposisi" class="w-full border rounded p-2" x-ref="nama_komposisi"
                @keydown.enter.prevent="$refs.deskripsi.focus()">
            @error('nama_komposisi')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label>Deskripsi</label>
            <textarea wire:model="deskripsi" class="w-full border rounded p-2" x-ref="deskripsi"
                @keydown.enter.prevent="$refs.submit.focus()"></textarea>
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded" x-ref="submit">
            {{ $komposisi_id ? 'Update' : 'Simpan' }}
        </button>
    </form>
</div>
