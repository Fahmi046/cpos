<div class="bg-white rounded-md shadow p-4 mb-4">
    <h2 class="text-base font-semibold text-gray-700 mb-3">Master Pabrik</h2>

    <form wire:submit.prevent="save" x-data x-init="$nextTick(() => $refs.nama_pabrik.focus())"
        x-on:focus-nama-pabrik.window="$refs.nama_pabrik.focus()" class="space-y-3 text-sm">

        <!-- Baris 1: Nama & Kode -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- Nama Pabrik -->
            <div class="md:col-span-2">
                <label for="nama_pabrik" class="block mb-1">Nama Pabrik</label>
                <input type="text" id="nama_pabrik" wire:model="nama_pabrik" x-ref="nama_pabrik"
                    @keydown.enter.prevent="$refs.alamat.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                @error('nama_pabrik')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kode Pabrik -->
            <div>
                <label for="kode_pabrik" class="block mb-1">Kode</label>
                <input type="text" id="kode_pabrik" wire:model="kode_pabrik" readonly
                    class="w-full rounded border-gray-200 bg-gray-100 text-gray-600 px-2 py-1 cursor-not-allowed" />
                @error('kode_pabrik')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Baris 2: Alamat -->
        <div>
            <label for="alamat" class="block mb-1">Alamat</label>
            <textarea id="alamat" wire:model="alamat" x-ref="alamat" @keydown.enter.prevent="$refs.telepon.focus()"
                class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1"></textarea>
            @error('alamat')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Baris 3: Telepon & Aktif -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
            <!-- Telepon -->
            <div class="md:col-span-2">
                <label for="telepon" class="block mb-1">Telepon</label>
                <input type="text" id="telepon" wire:model="telepon" x-ref="telepon"
                    @keydown.enter.prevent="$refs.checkbox.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                @error('telepon')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Aktif -->
            <div class="flex items-center gap-2 mt-5">
                <input type="checkbox" id="aktif" wire:model="aktif" x-ref="checkbox"
                    @keydown.enter.prevent="$refs.submit.focus()"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <label for="aktif" class="text-sm text-gray-700">Aktif</label>
            </div>
        </div>

        <!-- Tombol -->
        <div class="flex gap-2">
            <button type="submit" x-ref="submit"
                class="w-full md:w-auto px-4 py-1.5 rounded text-white bg-blue-600 hover:bg-blue-700 text-sm">
                {{ $pabrik_id ? 'Update' : 'Simpan' }}
            </button>
            <button type="button" wire:click="resetForm"
                class="w-full md:w-auto px-4 py-1.5 rounded text-gray-700 bg-gray-200 hover:bg-gray-300 text-sm">
                Batal
            </button>
        </div>

    </form>
</div>
