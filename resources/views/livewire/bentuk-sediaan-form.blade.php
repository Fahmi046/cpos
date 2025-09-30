<div class="bg-white rounded-md shadow p-4 mb-4">
    <h2 class="text-base font-semibold text-gray-700 mb-3">Master Sediaan</h2>

    <form wire:submit.prevent="save" x-data x-init="$nextTick(() => $refs.nama_sediaan.focus())"
        x-on:focus-nama-sediaan.window="$refs.nama_sediaan.focus()" class="space-y-3 text-sm">

        <!-- Baris 1: Nama & Kode -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- Nama Sediaan -->
            <div class="md:col-span-2">
                <label for="nama_sediaan" class="block mb-1">Nama Sediaan</label>
                <input type="text" id="nama_sediaan" wire:model="nama_sediaan" x-ref="nama_sediaan"
                    @keydown.enter.prevent="$refs.deskripsi.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                @error('nama_sediaan')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kode Sediaan -->
            <div>
                <label for="kode_sediaan" class="block mb-1">Kode</label>
                <input type="text" id="kode_sediaan" wire:model="kode_sediaan" readonly
                    class="w-full rounded border-gray-200 bg-gray-100 text-gray-600 px-2 py-1 cursor-not-allowed" />
                @error('kode_sediaan')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Baris 2: Deskripsi -->
        <div>
            <label for="deskripsi" class="block mb-1">Deskripsi</label>
            <textarea id="deskripsi" wire:model="deskripsi" x-ref="deskripsi" @keydown.enter.prevent="$refs.checkbox.focus()"
                class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1"></textarea>
            @error('deskripsi')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Baris 3: Aktif -->
        <div class="flex items-center gap-2">
            <input type="checkbox" id="aktif" wire:model="aktif" x-ref="checkbox"
                @keydown.enter.prevent="$refs.submit.focus()"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
            <label for="aktif" class="text-sm text-gray-700">Aktif</label>
        </div>

        <!-- Tombol -->
        <div class="flex gap-2">
            <button type="submit" x-ref="submit"
                class="w-full md:w-auto px-4 py-1.5 rounded text-white bg-blue-600 hover:bg-blue-700 text-sm">
                {{ $sediaan_id ? 'Update' : 'Simpan' }}
            </button>
            <button type="button" wire:click="resetForm"
                class="w-full md:w-auto px-4 py-1.5 rounded text-gray-700 bg-gray-200 hover:bg-gray-300 text-sm">
                Batal
            </button>
        </div>

    </form>
</div>
