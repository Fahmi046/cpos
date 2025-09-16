<div class="p-4 border rounded mb-6">
    <h2 class="text-lg font-bold mb-4">{{ $isEdit ? 'Edit Outlet' : 'Tambah Outlet' }}</h2>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="text" wire:model="kode_outlet" placeholder="Kode Outlet" class="border p-2 rounded w-full">
        <input type="text" wire:model="nama_outlet" placeholder="Nama Outlet" class="border p-2 rounded w-full">
        <input type="text" wire:model="alamat" placeholder="Alamat" class="border p-2 rounded w-full">
        <input type="text" wire:model="telepon" placeholder="Telepon" class="border p-2 rounded w-full">
        <input type="text" wire:model="pic" placeholder="PIC" class="border p-2 rounded w-full">
        <select wire:model="aktif" class="border p-2 rounded w-full">
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
        </select>
    </div>

    <div class="mt-4 flex gap-2">
        @if ($isEdit)
            <button wire:click="update" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
            <button wire:click="resetInput" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
        @else
            <button wire:click="store" class="bg-green-500 text-white px-4 py-2 rounded">Simpan</button>
        @endif
    </div>
</div>
