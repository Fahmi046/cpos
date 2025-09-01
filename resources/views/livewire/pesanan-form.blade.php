<div>
    <div class="p-4 bg-white rounded shadow">
        <form wire:submit.prevent="save">
            <div class="mb-2">
                <label class="block font-semibold">No SP</label>
                <input type="text" wire:model="no_sp" class="border rounded w-full p-2 bg-gray-100" readonly>
            </div>

            <div class="mb-2">
                <label class="block font-semibold">Tanggal</label>
                <input type="date" wire:model="tanggal" class="border rounded w-full p-2">
            </div>

            <div class="mb-2">
                <label class="block font-semibold">Kategori</label>
                <select wire:model.live="kategori" id="kategori" class="border rounded w-full p-2">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="OOT">OOT</option>
                    <option value="PREK">PREK</option>
                    <option value="PSIKO">PSIKO</option>
                    <option value="NARKO">NARKO</option>
                </select>
            </div>

            <!-- Detail Obat -->
            <h3 class="font-bold mt-4 mb-2">Detail Obat</h3>
            @foreach ($details as $index => $detail)
                <div class="grid grid-cols-12 gap-2 mb-2" wire:key="detail-{{ $index }}">
                    <!-- Nama Obat (lebih lebar) -->
                    <select wire:model.live="details.{{ $index }}.obat_id" class="border rounded p-2 col-span-4">
                        <option value="">-- Pilih Obat --</option>
                        @foreach ($obatList as $obat)
                            <option value="{{ $obat->id }}">{{ $obat->nama_obat }}</option>
                        @endforeach
                    </select>

                    <!-- Qty -->
                    <input type="number" wire:model.live="details.{{ $index }}.qty" placeholder="Qty"
                        class="border rounded p-2 col-span-1">

                    <!-- Harga -->
                    <input type="number" wire:model="details.{{ $index }}.harga" placeholder="Harga"
                        class="border rounded p-2 col-span-2" readonly>

                    <!-- Isi Obat -->
                    <input type="number" wire:model="details.{{ $index }}.isi" placeholder="Isi"
                        class="border rounded p-2 col-span-1 bg-gray-100" readonly>

                    <!-- Satuan -->
                    <input type="text" wire:model="details.{{ $index }}.satuan" placeholder="Satuan"
                        class="border rounded p-2 col-span-1 bg-gray-100" readonly>

                    <!-- Jumlah -->
                    <input type="number" wire:model="details.{{ $index }}.jumlah" placeholder="Jumlah"
                        class="border rounded p-2 col-span-2 bg-gray-100" readonly>

                    <button type="button" wire:click="removeDetail({{ $index }})"
                        class="bg-red-500 text-white px-2 rounded col-span-1">X</button>
                </div>
            @endforeach


            <button type="button" wire:click="addDetail" class="bg-blue-500 text-white px-4 py-1 rounded mb-4">+ Tambah
                Obat</button>

            <div class="mt-4">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
