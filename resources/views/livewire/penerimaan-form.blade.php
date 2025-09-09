<div class="max-w-7xl mx-auto p-4 bg-white rounded-lg shadow-md">
    <form wire:submit.prevent="save" class="space-y-6">

        {{-- 🔹 FORM PENERIMAAN --}}
        <div class="grid grid-cols-11 gap-4">

            <div class="col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">No Penerimaan</label>
                <input type="text" wire:model="no_penerimaan"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
            </div>

            {{-- Pesanan --}}
            <div class="col-span-5">
                <label class="block mb-2 text-sm font-medium text-gray-900">Pesanan</label>
                <select wire:model="pesanan_id" wire:change="loadPesanan"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">-- Pilih Pesanan --</option>
                    @foreach ($pesananList as $pesanan)
                        <option value="{{ $pesanan->id }}">
                            {{ $pesanan->no_sp }} - {{ $pesanan->tanggal }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Terima</label>
                <input type="date" wire:model="tanggal"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div class="col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">Jenis Bayar</label>
                <select wire:model="jenis_bayar"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">-- Pilih --</option>
                    <option value="CASH">CASH</option>
                    <option value="KREDIT">KREDIT</option>
                </select>
            </div>

            <div class="col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">Kreditur</label>
                <select wire:model="kreditur_id"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">-- Pilih Kreditur --</option>
                    {{--  @foreach ($krediturList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach  --}}
                </select>
            </div>

            <div class="col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">No Faktur</label>
                <input type="text" wire:model="no_faktur"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div class="col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Faktur</label>
                <input type="date" wire:model="tanggal"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div class="col-span-1">
                <label class="block mb-2 text-sm font-medium text-gray-900">Tenor (hari)</label>
                <input type="number" wire:model="tenor"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div class="col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">Jatuh Tempo</label>
                <input type="date" wire:model="jatuh_tempo"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div class="col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">Jenis PPN</label>
                <select wire:model="jenis_ppn"
                    class="w-full p-2.5 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option value="">-- Pilih --</option>
                    <option value="INCLUDE">INCLUDE</option>
                    <option value="EXCLUDE">EXCLUDE</option>
                    <option value="NON">NON</option>
                </select>
            </div>
        </div>

        {{-- 🔹 DETAIL PENERIMAAN --}}
        <div class="mt-6">
            <h3 class="mb-3 text-lg font-semibold text-gray-800">Detail Penerimaan</h3>

            <div class="space-y-3">
                @forelse ($details as $i => $detail)
                    <div class="grid grid-cols-10 gap-3 items-end border rounded-lg p-3 bg-gray-50">
                        {{-- Obat --}}
                        <div class="col-span-3">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Obat</label>
                            <select wire:model="details.{{ $i }}.obat_id"
                                class="w-full p-2 border rounded-lg">
                                <option value="">-- Pilih --</option>
                                @foreach ($obatList as $o)
                                    <option value="{{ $o->id }}">{{ $o->nama_obat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- isi obat --}}
                        <div class="col-span-1">
                            <label class="block mb-1 text-xs font-medium text-gray-700">isi obat</label>
                            <input type="number" min="0" wire:model="details.{{ $i }}.isi_obat"
                                class="w-full p-2 border rounded-lg text-center">
                        </div>


                        {{-- utuhan --}}
                        <div class="col-span-1 flex flex-col items-center">
                            <label for="utuh-{{ $i }}"
                                class="mb-2 text-sm font-medium text-gray-900">Utuh</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input id="utuh-{{ $i }}" type="checkbox"
                                    wire:model="details.{{ $i }}.utuh" class="sr-only peer">
                                <div
                                    class="relative w-14 h-8 bg-gray-300 rounded-full peer-focus:ring-4 peer-focus:ring-blue-300 peer
                    peer-checked:bg-green-500
                    after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white
                    after:border-gray-300 after:border after:rounded-full after:h-7 after:w-7 after:transition-all
                    peer-checked:after:translate-x-6">
                                </div>
                            </label>
                        </div>


                        {{-- Satuan --}}
                        <div class="col-span-2">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Satuan</label>
                            <select wire:model="details.{{ $i }}.satuan_id"
                                class="w-full p-2 border rounded-lg">
                                <option value="">-- Pilih --</option>
                                @foreach ($satuanList as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Qty --}}
                        <div class="col-span-1">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Qty</label>
                            <input type="number" min="0" wire:model="details.{{ $i }}.qty"
                                class="w-full p-2 border rounded-lg text-center">
                        </div>


                        {{-- harga --}}
                        <div class="col-span-2">
                            <label class="block mb-1 text-xs font-medium text-gray-700">harga</label>
                            <input type="number" min="0" wire:model="details.{{ $i }}.harga"
                                class="w-full p-2 border rounded-lg text-center">
                        </div>

                        {{-- ED --}}
                        <div class="col-span-2">
                            <label class="block mb-1 text-xs font-medium text-gray-700">ED</label>
                            <input type="date" wire:model="details.{{ $i }}.ed"
                                class="w-full p-2 border rounded-lg">
                        </div>

                        {{-- Batch --}}
                        <div class="col-span-2">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Batch</label>
                            <input type="text" wire:model="details.{{ $i }}.batch"
                                class="w-full p-2 border rounded-lg">
                        </div>

                        {{-- Disc 1 --}}
                        <div class="col-span-1">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Disc 1</label>
                            <input type="number" min="0" wire:model="details.{{ $i }}.disc1"
                                class="w-full p-2 border rounded-lg text-right">
                        </div>

                        {{-- Disc 2 --}}
                        <div class="col-span-1">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Disc 2</label>
                            <input type="number" min="0" wire:model="details.{{ $i }}.disc2"
                                class="w-full p-2 border rounded-lg text-right">
                        </div>

                        {{-- Disc 3 --}}
                        <div class="col-span-1">
                            <label class="block mb-1 text-xs font-medium text-gray-700">Disc 3</label>
                            <input type="number" min="0" wire:model="details.{{ $i }}.disc3"
                                class="w-full p-2 border rounded-lg text-right">
                        </div>

                        {{-- Hapus --}}
                        <div class="col-span-1 flex items-center justify-center mt-5">
                            <button type="button" wire:click="removeDetail({{ $i }})"
                                class="px-2 py-1 text-xs text-white bg-red-500 rounded-lg hover:bg-red-600">
                                ✕
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada baris detail</p>
                @endforelse
            </div>

            <div class="mt-3">
                <button type="button" wire:click="addDetail"
                    class="px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700">
                    + Tambah Baris
                </button>
            </div>
        </div>


        {{-- 🔹 SIMPAN --}}
        <div class="flex justify-end">
            <button type="submit"
                class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300">
                Simpan
            </button>
        </div>
    </form>
</div>
