<div class="bg-white rounded-md shadow p-4 mb-4">
    <h2 class="text-base font-semibold text-gray-700 mb-3">Master Kreditur</h2>

    <form wire:submit.prevent="save" x-data x-init="$nextTick(() => $refs.nama.focus())" x-on:focus-nama.window="$refs.nama.focus()"
        class="space-y-3 text-sm">

        <!-- Baris 1: Nama, Kode, Telepon -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- Nama Kreditur -->
            <div class="md:col-span-2">
                <label for="nama" class="block mb-1">Nama Kreditur</label>
                <input type="text" id="nama" wire:model="nama" x-ref="nama"
                    @keydown.enter.prevent="$refs.alamat.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                @error('nama')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kode Kreditur -->
            <div>
                <label for="kode_kreditur" class="block mb-1">Kode</label>
                <input type="text" id="kode_kreditur" wire:model="kode_kreditur" readonly
                    class="w-full rounded border-gray-200 bg-gray-100 text-gray-600 px-2 py-1 cursor-not-allowed" />
                @error('kode_kreditur')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Baris 2: Alamat & Telepon -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label for="alamat" class="block mb-1">Alamat</label>
                <input type="text" id="alamat" wire:model="alamat" x-ref="alamat"
                    @keydown.enter.prevent="$refs.telepon.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
            </div>
            <div>
                <label for="telepon" class="block mb-1">Telepon</label>
                <input type="text" id="telepon" wire:model="telepon" x-ref="telepon"
                    @keydown.enter.prevent="$refs.email.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
            </div>
        </div>

        <!-- Baris 3: Email & Status + Tombol -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-start">
            <div class="md:col-span-2">
                <label for="email" class="block mb-1">Email</label>
                <input type="email" id="email" wire:model="email" x-ref="email"
                    @keydown.enter.prevent="$refs.aktif.focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                @error('email')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-2">
                <div>
                    <label for="aktif" class="block mb-1">Status</label>
                    <select id="aktif" wire:model="aktif" x-ref="aktif"
                        @keydown.enter.prevent="$refs.submit.focus()"
                        class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" x-ref="submit"
                        class="w-full px-3 py-1.5 rounded text-white bg-green-600 hover:bg-green-700 text-sm">
                        Simpan
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
