<div class="p-4 mb-4 bg-white rounded-md shadow">
    <h2 class="mb-3 text-base font-semibold text-gray-700">Form Kategori Harga</h2>

    <form wire:submit.prevent="save" x-data x-init="$nextTick(() => $refs.nama.focus())"
        class="flex flex-col items-start gap-3 text-sm md:flex-row md:items-end">

        <!-- Nama -->
        <div class="flex-1">
            <label for="nama" class="block mb-1">Nama Kategori</label>
            <input type="text" id="nama" wire:model="nama" x-ref="nama"
                @keydown.enter.prevent="$refs.persentase.focus()"
                class="w-full px-2 py-1 border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" />
            @error('nama')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Persentase -->
        <div class="w-32">
            <label for="persentase" class="block mb-1">Persentase (%)</label>
            <input type="number" step="0.01" id="persentase" wire:model="persentase" x-ref="persentase"
                @keydown.enter.prevent="$refs.tipe.focus()"
                class="w-full px-2 py-1 text-right border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" />
            @error('persentase')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tipe -->
        <div class="w-40">
            <label for="tipe" class="block mb-1">Tipe</label>
            <select id="tipe" wire:model="tipe" x-ref="tipe" @keydown.enter.prevent="$refs.submit.focus()"
                class="w-full px-2 py-1 border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Pilih --</option>
                <option value="Naik">Naikkan Harga</option>
                <option value="Turun">Turunkan Harga</option>
            </select>
            @error('tipe')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>


        <!-- Tombol Simpan -->
        <div class="flex mt-1 md:mt-0">
            <button type="submit" x-ref="submit"
                class="px-4 py-1.5 rounded text-white bg-blue-600 hover:bg-blue-700 text-sm">
                Simpan
            </button>
        </div>

    </form>
</div>
