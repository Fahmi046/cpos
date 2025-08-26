<div class="p-4 border rounded bg-gray-50">
    <form wire:submit.prevent="save" x-data x-init="$nextTick(() => $refs.nama.focus())" x-on:focus-nama.window="$refs.nama.focus()">
        <div class="mb-2">
            <label class="block">Kode Kreditur</label>
            <input type="text" wire:model="kode_kreditur" class="border rounded px-2 py-1 w-full cursor-not-allowed"
                readonly>
            @error('kode_kreditur')
                <span class="text-red-600">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-2">
            <label class="block">Nama Kreditur</label>
            <input type="text" wire:model="nama" class="border rounded px-2 py-1 w-full" x-ref="nama"
                @keydown.enter.prevent="$refs.alamat.focus()">
            @error('nama_kreditur')
                <span class="text-red-600">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-2">
            <label class="block">Alamat</label>
            <input type="text" wire:model="alamat" class="border rounded px-2 py-1 w-full" x-ref="alamat"
                @keydown.enter.prevent="$refs.telepon.focus()">
        </div>

        <div class="mb-2">
            <label class="block">Telepon</label>
            <input type="text" wire:model="telepon" class="border rounded px-2 py-1 w-full" x-ref="telepon"
                @keydown.enter.prevent="$refs.email.focus()">
        </div>

        <div class="mb-2">
            <label class="block">Email</label>
            <input type="email" wire:model="email" class="border rounded px-2 py-1 w-full" x-ref="email"
                @keydown.enter.prevent="$refs.checkbox.focus()">
            @error('email')
                <span class="text-red-600">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-2">
            <label class="inline-flex items-center">
                <input type="checkbox" wire:model="aktif" class="mr-2" x-ref="checkbox"
                    @keydown.enter.prevent="$refs.submit.focus()"> Aktif
            </label>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded" x-ref="submit">Simpan</button>
        </div>
    </form>
</div>
