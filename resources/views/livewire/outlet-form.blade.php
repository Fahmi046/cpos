<div class="bg-white rounded-md shadow p-4 mb-4">
    <h2 class="text-base font-semibold text-gray-700 mb-3">Master Outlet</h2>

    <form wire:submit.prevent="save" class="space-y-3 text-sm">
        <!-- Baris 1: Nama, Kode, Telepon, PIC -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label for="nama_outlet" class="block mb-1">Nama Outlet</label>
                <input x-ref="nama_outlet" @keydown.enter.prevent="$refs.telepon.focus()" type="text" id="nama_outlet"
                    wire:model="nama_outlet" autofocus
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                @error('nama_outlet')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="kode_outlet" class="block mb-1">Kode</label>
                <input type="text" id="kode_outlet" wire:model="kode_outlet"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                @error('kode_outlet')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="telepon" class="block mb-1">Telepon</label>
                <input x-ref="telepon" @keydown.enter.prevent="$refs.pic.focus()" type="text" id="telepon"
                    wire:model="telepon"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
            </div>
        </div>

        <!-- Baris 2: PIC, Alamat, Status + Tombol -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-start">
            <!-- PIC -->
            <div>
                <label for="pic" class="block mb-1">PIC</label>
                <input x-ref="pic" @keydown.enter.prevent="$refs.alamat.focus()" type="text" id="pic"
                    wire:model="pic"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
            </div>

            <!-- Alamat -->
            <div class="md:col-span-2">
                <label for="alamat" class="block mb-1">Alamat</label>
                <input x-ref="alamat" @keydown.enter.prevent="$refs.aktif.focus()" type="text" id="alamat"
                    wire:model="alamat"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
            </div>

            <!-- Status + Tombol -->
            <div class="flex flex-col gap-2">
                <div>
                    <label class="block mb-1">Status</label>
                    <select x-ref="aktif" @keydown.enter.prevent="$refs.submit.focus()" wire:model="aktif"
                        class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <!-- Tombol full width -->
                <div class="flex gap-2">
                    <button x-ref="submit" type="submit"
                        class="w-full px-3 py-1.5 rounded text-white bg-blue-600 hover:bg-blue-700 text-sm">
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
