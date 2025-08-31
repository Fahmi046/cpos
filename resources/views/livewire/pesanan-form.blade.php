<div>
    <div class="p-4 bg-white rounded shadow">
        <form wire:submit.prevent="save">
            <div class="mb-2">
                <label>No SP</label>
                <input type="text" wire:model="no_sp" class="border rounded w-full">
            </div>
            <div class="mb-2">
                <label>Tanggal</label>
                <input type="date" wire:model="tanggal" class="border rounded w-full">
            </div>

            <h3 class="font-bold mt-4">Detail Obat</h3>
            @foreach ($details as $index => $detail)
                <div class="grid grid-cols-5 gap-2 mb-2">
                    <select wire:model="details.{{ $index }}.obat_id" class="border rounded">
                        <option value="">--Pilih Obat--</option>
                        @foreach ($obatList as $obat)
                            <option value="{{ $obat->id }}">{{ $obat->nama_obat }}</option>
                        @endforeach
                    </select>
                    <input type="number" wire:model="details.{{ $index }}.qty" placeholder="Qty"
                        class="border rounded">
                    <input type="number" wire:model="details.{{ $index }}.harga" placeholder="Harga"
                        class="border rounded">
                    <input type="number" wire:model="details.{{ $index }}.jumlah" placeholder="Jumlah"
                        class="border rounded" readonly>
                    <button type="button" wire:click="removeDetail({{ $index }})"
                        class="bg-red-500 text-white px-2 rounded">X</button>
                </div>
            @endforeach
            <button type="button" wire:click="addDetail" class="bg-blue-500 text-white px-4 py-1 rounded">+ Tambah
                Obat</button>

            <div class="mt-4">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>

</div>
