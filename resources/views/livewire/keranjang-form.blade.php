<div class="min-h-screen p-6 bg-gray-50">
    <!-- Header -->
    <div x-data x-init="$nextTick(() => $refs.obat.focus())" x-on:focus-nama-obat.window="$refs.obat.focus()"
        class="flex flex-col gap-3 mb-6 md:flex-row md:items-center md:gap-4">
        <!-- Pilihan Obat -->
        <div class="relative w-full md:w-1/3">
            <input type="text" x-ref="obat"
                @keydown.enter.prevent="
                $wire.selectHighlightedObat();
                $wire.showObatDropdown = false;
                setTimeout(() => $refs.kategori.focus(), 150);
            "
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                placeholder="Cari obat..." wire:model.debounce.300ms="nama_obat"
                wire:input="searchObat($event.target.value); resetHighlight()" @focus="$wire.showObatDropdown = true"
                @keydown.arrow-down.prevent="$wire.incrementHighlight()"
                @keydown.arrow-up.prevent="$wire.decrementHighlight()">

            @if (!empty($obatSearch) && $showObatDropdown)
                <div class="absolute z-10 w-full mt-1 overflow-y-auto bg-white border rounded shadow max-h-48">
                    @foreach ($obatSearch as $i => $obat)
                        @php $isActive = $highlightedIndex === $i; @endphp
                        <div class="px-3 py-2 text-sm cursor-pointer transition-colors duration-100
                    {{ $isActive ? 'bg-indigo-500 text-white' : 'hover:bg-indigo-100 text-gray-900' }}"
                            wire:click="selectObat({{ $obat->id }})">
                            <div class="font-medium">{{ $obat->nama }}</div>
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
        <select wire:model="kategori_harga_id" wire:change="updateHargaKategori" x-ref="kategori"
            @keydown.enter.prevent="$refs.qty.focus()"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg md:w-1/4 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            <option value="">Kategori Harga</option>
            @foreach ($kategoriHargas as $kh)
                <option value="{{ $kh->id }}">{{ $kh->nama }}</option>
            @endforeach
        </select>

        <!-- Nilai Persen -->
        <input type="number" wire:model="persen_kategori" placeholder="%" x-ref="persen"
            @keydown.enter.prevent="$refs.harga.focus()"
            class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none"
            readonly>

        <!-- Harga -->
        <input type="text" wire:model="harga_tampil" placeholder="Harga" x-ref="harga"
            @keydown.enter.prevent="$refs.qty.focus()"
            class="w-32 px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none"
            readonly>

        <!-- Qty -->
        <input type="number" wire:model.live="qty" min="1" placeholder="Qty" x-ref="qty"
            @keydown.enter.prevent="$refs.tambah.focus()"
            class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">

        <!-- Subtotal -->
        <input type="text" wire:model="subtotal_tampil" placeholder="Subtotal" x-ref="subtotal"
            @keydown.enter.prevent="$refs.tambah.click()"
            class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg w-36 focus:ring-2 focus:ring-indigo-400 focus:outline-none"
            readonly>

        <!-- Tombol Tambah & Reset -->
        <div class="flex gap-2">
            <button wire:click="addToKeranjang" x-ref="tambah"
                class="flex items-center gap-1 px-4 py-2 text-sm font-semibold text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah
            </button>

            <button wire:click="resetInputObat"
                class="px-4 py-2 text-sm font-semibold text-indigo-600 transition border border-indigo-600 rounded-lg hover:bg-indigo-50">
                🔄
            </button>
        </div>
    </div>


    <!-- Judul -->
    <h2 class="mb-4 text-2xl font-bold text-gray-800">🧺 Daftar Keranjang Belanja</h2>

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

                            <!-- Nama Obat -->
                            <td class="px-4 py-3 font-medium text-gray-800">
                                <div>{{ $item->obat->nama_obat ?? ($item->nama_obat ?? '-') }}</div>
                                <div class="text-xs text-gray-500">
                                    Batch: {{ $item->obat->batch ?? '-' }} |
                                    ED: {{ \Carbon\Carbon::parse($item->obat->ed ?? '')->format('d/m/Y') ?? '-' }}
                                </div>
                            </td>


                            <!-- Kategori Harga + Persen -->
                            <td class="px-4 py-3 text-gray-700">
                                @if ($item->kategoriHarga)
                                    {{ $item->kategoriHarga->nama }}
                                    @if ($item->kategoriHarga->persentase)
                                        <span class="text-sm text-gray-500">
                                            ({{ $item->kategoriHarga->persentase }}%)
                                        </span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Qty -->
                            <td class="px-4 py-3 text-center text-gray-700">
                                {{ $item->qty }}
                            </td>

                            <!-- Harga -->
                            <td class="px-4 py-3 font-semibold text-right text-gray-800">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </td>

                            <!-- Subtotal -->
                            <td class="px-4 py-3 font-semibold text-right text-indigo-600">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>

                            <!-- Aksi -->
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
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('focus-nama-obat', () => {
            requestAnimationFrame(() => {
                const input = document.querySelector(
                    'input[wire\\:model\\.debounce\\.300ms="nama_obat"]');
                if (input) {
                    input.focus();
                    input.select();
                }
            });
        });
    });
</script>
