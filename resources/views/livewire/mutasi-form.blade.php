<div class="p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4">Input Mutasi Stok</h2>

    {{-- Form --}}
    <form wire:submit.prevent="save">
        {{-- Header --}}
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label>No Mutasi</label>
                <input type="text" wire:model="no_mutasi" class="w-full border rounded p-2" readonly>
            </div>
            <div>
                <label>Tanggal</label>
                <input type="date" wire:model="tanggal" class="w-full border rounded p-2">
            </div>
            <div>
                <label>Outlet Tujuan</label>
                <select wire:model="outlet_id" class="w-full border rounded p-2">
                    <option value="">-- pilih outlet --</option>
                    @foreach ($outlets as $o)
                        <option value="{{ $o->id }}">{{ $o->nama_outlet }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Keterangan</label>
                <input type="text" wire:model="keterangan" class="w-full border rounded p-2">
            </div>
        </div>

        {{-- Detail --}}
        <h3 class="font-semibold mb-2">Tambah Barang</h3>
        <div class="grid grid-cols-4 gap-2 mb-2">
            <select wire:model="obat_id" class="border rounded p-2">
                <option value="">-- pilih obat --</option>
                @foreach ($obats as $o)
                    <option value="{{ $o->id }}">{{ $o->nama_obat }}</option>
                @endforeach
            </select>
            <input type="number" wire:model="qty" placeholder="Qty" class="border rounded p-2">
            <input type="text" wire:model="batch" placeholder="Batch" class="border rounded p-2">
            <input type="date" wire:model="ed" placeholder="ED" class="border rounded p-2">
        </div>
        <button type="button" wire:click="addDetail"
            class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Tambah</button>

        {{-- List Detail --}}
        @if (!empty($detail))
            <div class="mb-4">
                <h4 class="font-semibold mb-2">Detail Barang</h4>
                <ul class="space-y-1">
                    @foreach ($detail as $index => $d)
                        <li class="flex justify-between bg-gray-50 border rounded p-2">
                            <span>{{ $d['nama_obat'] ?? '-' }} | Qty: {{ $d['qty'] }} | Batch:
                                {{ $d['batch'] ?? '-' }} | ED: {{ $d['ed'] ?? '-' }}</span>
                            <button type="button" wire:click="removeDetail({{ $index }})"
                                class="text-red-600 font-bold">×</button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Tombol Simpan --}}
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan Mutasi</button>
    </form>
</div>
