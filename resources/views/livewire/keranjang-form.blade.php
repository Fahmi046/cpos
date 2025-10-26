<div class="min-h-screen p-6 bg-gray-50">
    <!-- Header -->
    <div class="flex flex-col gap-3 mb-6 md:flex-row md:items-center md:gap-4">
        <!-- Pilihan Obat (Autocomplete) -->
        <div class="relative w-full md:w-1/3">
            <input type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                placeholder="Cari obat..." wire:model.debounce.300ms="nama_obat"
                wire:input="searchObat($event.target.value); resetHighlight()" @focus="$wire.showObatDropdown = true"
                @keydown.arrow-down.prevent="$wire.incrementHighlight()"
                @keydown.arrow-up.prevent="$wire.decrementHighlight()"
                @keydown.enter.prevent="$wire.selectHighlightedObat(); $wire.showObatDropdown = false" x-ref="obat_id">

            @if (!empty($obatSearch) && $showObatDropdown)
                <div class="absolute z-10 w-full mt-1 overflow-y-auto bg-white border rounded shadow max-h-48">
                    @foreach ($obatSearch as $i => $obat)
                        @php
                            $isActive = $highlightedIndex === $i;
                        @endphp

                        <div class="px-3 py-2 text-sm cursor-pointer transition-colors duration-100
                {{ $isActive ? 'bg-indigo-500 text-white' : 'hover:bg-indigo-100 text-gray-900' }}"
                            wire:click="selectObat({{ $obat->id }})">
                            <!-- Nama Obat -->
                            <div class="font-medium">
                                {{ $obat->nama }}
                            </div>

                            <!-- Info Batch, ED, dan Stok -->
                            <div class="text-xs {{ $isActive ? 'text-indigo-100' : 'text-gray-500' }}">
                                Batch: {{ $obat->batch ?? '-' }} |
                                ED: {{ $obat->ed == '-' ? '-' : \Carbon\Carbon::parse($obat->ed)->format('d/m/Y') }} |
                                Stok: {{ $obat->stok_akhir }}
                            </div>
                        </div>
                    @endforeach
                </div>

            @endif
        </div>


        <!-- Kategori Harga -->
        <select wire:model="kategori_harga_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg md:w-1/4 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            <option value="">Kategori Harga</option>
            @foreach ($kategoriHargas as $kh)
                <option value="{{ $kh->id }}">{{ $kh->nama }}</option>
            @endforeach
        </select>

        <!-- Qty -->
        <input type="number" wire:model="qty" min="1" placeholder="Qty"
            class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">

        <!-- Tombol Tambah -->
        <button wire:click="addToKeranjang"
            class="px-4 py-2 text-sm font-semibold text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
            ➕ Tambah
        </button>
    </div>

    <!-- Judul -->
    <h2 class="mb-4 text-2xl font-bold text-gray-800">🧺 Daftar Keranjang Obat</h2>

    <!-- Tabel -->
    <div class="overflow-hidden bg-white shadow-lg rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="text-xs text-gray-700 uppercase bg-indigo-50">
                    <tr>
                        <th class="px-4 py-3 text-center">No</th>
                        <th class="px-4 py-3">Nama Obat</th>
                        <th class="px-4 py-3">Kategori Harga</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($keranjangItems as $index => $item)
                        <tr class="transition hover:bg-indigo-50/50">
                            <td class="px-4 py-3 text-center text-gray-600">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $item->obat->nama ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $item->kategoriHarga->nama ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700">
                                {{ $item->qty }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-right text-gray-800">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-right text-indigo-600">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="removeItem({{ $item->id }})"
                                    class="p-2 text-red-600 transition bg-red-100 rounded-lg hover:bg-red-200"
                                    title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">
                                Belum ada item di keranjang 😔
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Total -->
        <div class="flex justify-end py-4 pr-6 bg-gray-50">
            <div class="px-4 py-2 text-lg font-bold text-gray-800 bg-white border rounded-lg shadow-sm">
                Total: <span class="text-indigo-600">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
