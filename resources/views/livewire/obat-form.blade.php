<div class="p-4 bg-white rounded shadow">
    <form wire:submit.prevent="store" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="kode_obat" class="block text-sm font-medium text-gray-700">Kode Obat</label>
                <input type="text" id="kode_obat" wire:model="kode_obat"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly />
            </div>
            <div>
                <label>Nama Obat</label>
                <input type="text" wire:model="nama_obat" class="w-full border rounded p-2">
                @error('nama_obat')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>Kategori</label>
                <input type="text" wire:model="kategori" class="w-full border rounded p-2">
            </div>
            <div>
                <label>Bentuk Sediaan</label>
                <input type="text" wire:model="bentuk_sediaan" placeholder="Tablet, Kapsul, Sirup..."
                    class="w-full border rounded p-2">
            </div>
        </div>

        <div>
            <label>Kandungan</label>
            <textarea wire:model="kandungan" class="w-full border rounded p-2"></textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label>Stok</label>
                <input type="number" wire:model="stok" class="w-full border rounded p-2">
            </div>
            <div>
                <label>Satuan</label>
                <input type="text" wire:model="satuan" placeholder="Strip, Botol, Box..."
                    class="w-full border rounded p-2">
            </div>
            <div>
                <label>Pabrik</label>
                <input type="text" wire:model="pabrik" class="w-full border rounded p-2">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label>Harga Beli</label>
                <input type="number" wire:model="harga_beli" step="0.01" class="w-full border rounded p-2">
            </div>
            <div>
                <label>Harga Jual</label>
                <input type="number" wire:model="harga_jual" step="0.01" class="w-full border rounded p-2">
            </div>
            <div>
                <label>Tanggal Expired</label>
                <input type="date" wire:model="tgl_expired" class="w-full border rounded p-2">
            </div>
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">
            {{ $obat_id ? 'Update' : 'Simpan' }}
        </button>
    </form>
</div>
