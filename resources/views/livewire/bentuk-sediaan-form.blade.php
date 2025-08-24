<div class="p-4 bg-white rounded shadow">
    <form wire:submit.prevent="save" class="space-y-4" x-data x-init="$nextTick(() => $refs.nama_sediaan.focus())"
        x-on:focus-nama-sediaan.window="$refs.nama_sediaan.focus()">
        <div>
            <label class="block text-sm">Kode Sediaan</label>
            <input type="text" wire:model="kode_sediaan" class="w-full border rounded p-2 cursor-not-allowed" readonly>
            @error('kode_sediaan')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm">Nama Sediaan</label>
            <input type="text" wire:model="nama_sediaan" class="w-full border rounded p-2" x-ref="nama_sediaan"
                @keydown.enter.prevent="$refs.deskripsi.focus()">
            @error('nama_sediaan')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm">Deskripsi</label>
            <textarea wire:model="deskripsi" x-ref="deskripsi" class="w-full border rounded p-2"
                @keydown.enter.prevent="$refs.checkbox.focus()"></textarea>
        </div>

        <div class="flex
                items-center gap-2">
            <input type="checkbox" wire:model="aktif" id="aktif" x-ref="checkbox"
                @keydown.enter.prevent="$refs.submit.focus()">
            <label for="aktif">Aktif</label>
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded" x-ref="submit">Simpan</button>
    </form>
</div>
