<div class="p-4 bg-white rounded-lg shadow">
    <form wire:submit.prevent="save" class="space-y-4" x-data x-init="$nextTick(() => $refs.nama_pabrik.focus())"
        x-on:focus-nama-pabrik.window="$refs.nama_pabrik.focus()" class="space-y-3">
        <div>
            <label>Kode Pabrik</label>
            <input type="text" wire:model="kode_pabrik" class="w-full border rounded p-2" readonly>
            @error('kode_pabrik')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label>Nama Pabrik</label>
            <input type="text" wire:model="nama_pabrik" class="w-full border rounded p-2" x-ref="nama_pabrik"
                @keydown.enter.prevent="$refs.alamat.focus()">
            @error('nama_pabrik')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label>Alamat</label>
            <textarea wire:model="alamat" class="w-full border rounded p-2" x-ref="alamat"
                @keydown.enter.prevent="$refs.telepon.focus()"></textarea>
        </div>

        <div>
            <label>Telepon</label>
            <input type="text" wire:model="telepon" class="w-full border rounded p-2" x-ref="telepon"
                @keydown.enter.prevent="$refs.checkbox.focus()">
        </div>

        <div>
            <label><input type="checkbox" wire:model="aktif" x-ref="checkbox"
                    @keydown.enter.prevent="$refs.submit.focus()"> Aktif</label>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded" x-ref="submit">Simpan</button>
    </form>
</div>
