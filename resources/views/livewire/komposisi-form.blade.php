<div class="bg-white rounded-md shadow p-4 mb-4">
    <h2 class="text-base font-semibold text-gray-700 mb-3">Master Komposisi</h2>

    <form wire:submit.prevent="store" x-data x-init="$nextTick(() => $refs.nama_komposisi.focus())"
        x-on:focus-nama-komposisi.window="$refs.nama_komposisi.focus()" class="space-y-3 text-sm">

        <!-- Baris 1: Nama & Kode -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- Nama Komposisi -->
            <div class="md:col-span-2">
                <label for="nama_komposisi" class="block mb-1">Nama Komposisi</label>
                <input type="text" id="nama_komposisi" wire:model="nama_komposisi" x-ref="nama_komposisi"
                    @keydown.enter.prevent="$refs.deskripsi.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                @error('nama_komposisi')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kode Komposisi -->
            <div>
                <label for="kode_komposisi" class="block mb-1">Kode</label>
                <input type="text" id="kode_komposisi" wire:model="kode_komposisi" readonly
                    class="w-full rounded border-gray-200 bg-gray-100 text-gray-600 px-2 py-1 cursor-not-allowed" />
                @error('kode_komposisi')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Baris 2: Deskripsi & Tombol -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-start">
            <div class="md:col-span-2">
                <label for="deskripsi" class="block mb-1">Deskripsi</label>
                <textarea id="deskripsi" wire:model="deskripsi" x-ref="deskripsi" @keydown.enter.prevent="$refs.submit.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1"></textarea>
            </div>

            <div class="flex flex-col gap-2">
                <div class="flex gap-2">
                    <button type="submit" x-ref="submit"
                        class="w-full px-3 py-1.5 rounded text-white bg-blue-600 hover:bg-blue-700 text-sm">
                        {{ $komposisi_id ? 'Update' : 'Simpan' }}
                    </button>
                    <button type="button" wire:click="resetForm"
                        class="w-full px-3 py-1.5 rounded text-gray-700 bg-gray-200 hover:bg-gray-300 text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
