<div x-data x-init="$nextTick(() => { $el.querySelector('#nama_satuan').focus() })" x-on:focus-nama-satuan.window="$refs.nama_satuan.focus()">
    <form wire:submit.prevent="save" id="satuanForm" class="space-y-4">

        <!-- Kode Satuan -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700">Kode Satuan</label>
            <input type="text" wire:model="kode_satuan" readonly
                class="w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed">
        </div>

        <!-- Nama Satuan -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700">Nama Satuan</label>
            <input type="text" wire:model="nama_satuan" id="nama_satuan"
                x-on:keydown.enter.prevent="$el.closest('form').querySelector('#deskripsi').focus()"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-indigo-300">
        </div>

        <!-- Deskripsi -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700">Deskripsi</label>
            <textarea wire:model="deskripsi" id="deskripsi"
                x-on:keydown.enter.prevent="$el.closest('form').querySelector('#aktif').focus()"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-indigo-300"></textarea>
        </div>

        <!-- Aktif -->
        <div>
            <label class="flex items-center space-x-2">
                <input type="checkbox" wire:model="aktif" id="aktif"
                    x-on:keydown.enter.prevent="$el.closest('form').querySelector('#btnSimpan').focus()">
                <span>Aktif</span>
            </label>
        </div>

        <!-- Tombol Simpan -->
        <div>
            <button type="submit" id="btnSimpan"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Simpan
            </button>
        </div>

    </form>
</div>
