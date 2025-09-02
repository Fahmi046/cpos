<div x-data
    @keydown.window="
    if ($event.key === 'F10') {
        $event.preventDefault();
        $el.querySelector('form').dispatchEvent(new Event('submit', {cancelable: true, bubbles: true}));
    }
">
    <div class="p-4 bg-white rounded shadow">
        <form wire:submit.prevent="save" x-data x-init="$nextTick(() => $refs.tanggal.focus())" x-on:focus-tanggal.window="$refs.tanggal.focus()"
            x-on:focus-row.window="
        $nextTick(() => {
            const el = $refs['obat_id_' + $event.detail.index];
            if (el) el.focus();
        })
    ">
            <div class="mb-2">
                <label class="block font-semibold">No SP</label>
                <input type="text" wire:model="no_sp" class="border rounded w-full p-2 bg-gray-100" readonly>
            </div>

            <div class="mb-2">
                <label class="block font-semibold">Tanggal</label>
                <input type="date" wire:model="tanggal" x-ref="tanggal"
                    @keydown.enter.prevent="$refs.kategori.focus()" class="border rounded w-full p-2">
            </div>

            <div class="mb-2">
                <label class="block font-semibold">Kategori</label>
                <select wire:model.live="kategori" id="kategori" x-ref="kategori"
                    @keydown.enter.prevent="$refs['obat_id_0'].focus()" class="border rounded w-full p-2">
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
                <div class="grid grid-cols-12 gap-2 mb-2 items-center" wire:key="detail-{{ $index }}">

                    <!-- Nama Obat AutoComplete -->
                    <div class="relative col-span-4">
                        <input type="text" class="border rounded p-2 w-full" placeholder="Cari obat..."
                            wire:model.defer="details.{{ $index }}.nama_obat"
                            wire:input="searchObat({{ $index }}, $event.target.value); resetHighlight({{ $index }})"
                            @focus="$wire.showObatDropdown[{{ $index }}] = true"
                            @keydown.arrow-down.prevent="$wire.incrementHighlight({{ $index }})"
                            @keydown.arrow-up.prevent="$wire.decrementHighlight({{ $index }})"
                            @keydown.enter.prevent="$wire.selectHighlightedObat({{ $index }}); $wire.showObatDropdown[{{ $index }}] = false; $refs['qty_{{ $index }}']?.focus();"
                            x-ref="obat_id_{{ $index }}">

                        @if (!empty($obatSearch[$index]) && ($showObatDropdown[$index] ?? false))
                            <div
                                class="absolute z-10 w-full bg-white border rounded mt-1 shadow-lg max-h-40 overflow-y-auto">
                                @foreach ($obatSearch[$index] as $i => $obat)
                                    <div class="p-2 cursor-pointer {{ ($highlightedIndex[$index] ?? 0) === $i ? 'bg-blue-100' : '' }}"
                                        wire:click="selectObat({{ $index }}, {{ $obat->id }})"
                                        wire:keydown.enter="$wire.selectObat({{ $index }}, {{ $obat->id }})">
                                        {{ $obat->nama_obat }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>



                    <!-- Qty -->
                    <div class="col-span-1">
                        <input type="number" wire:model.live="details.{{ $index }}.qty"
                            x-ref="qty_{{ $index }}" @keydown.enter.prevent="$refs.addDetail?.focus()"
                            placeholder="Qty" class="border rounded p-2 w-full text-center" />
                    </div>

                    <!-- Harga -->
                    <div class="col-span-2">
                        <input type="number" wire:model="details.{{ $index }}.harga" placeholder="Harga"
                            class="border rounded p-2 w-full text-right" readonly>
                    </div>

                    <!-- Isi -->
                    <div class="col-span-1">
                        <input type="number" wire:model="details.{{ $index }}.isi" placeholder="Isi"
                            class="border rounded p-2 w-full text-center bg-gray-100" readonly>
                    </div>

                    <!-- Satuan -->
                    <div class="col-span-1">
                        <input type="text" wire:model="details.{{ $index }}.satuan" placeholder="Satuan"
                            class="border rounded p-2 w-full text-center bg-gray-100" readonly>
                    </div>

                    <!-- Jumlah -->
                    <div class="col-span-2">
                        <input type="number" wire:model="details.{{ $index }}.jumlah" placeholder="Jumlah"
                            class="border rounded p-2 w-full text-right bg-gray-100" readonly>
                    </div>

                    <!-- Tombol Hapus -->
                    <div class="col-span-1 flex justify-center">
                        <button type="button" wire:click="removeDetail({{ $index }})"
                            class="bg-red-500 text-white px-3 py-1 rounded">X</button>
                    </div>
                </div>
            @endforeach


            <!-- Tombol Tambah -->
            <button type="button" wire:click="addDetail" x-ref="addDetail"
                class="bg-blue-500 text-white px-4 py-1 rounded mb-4">
                + Tambah Obat
            </button>

            <!-- Alpine Effect Fokus Otomatis -->
            <div x-data="{ rowCount: {{ count($details) }} }"
                x-effect="
        if (rowCount !== {{ count($details) }}) {
            rowCount = {{ count($details) }};
            $nextTick(() => {
                $refs['obat_id_' + (rowCount - 1)]?.focus();
            });
        }
     ">
            </div>


            <div class="mt-4">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Simpan (F10)
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('focus-obat', ({
            index
        }) => {
            setTimeout(() => {
                document.querySelector(`[x-ref='obat_id_${index}']`)?.focus();
            }, 50);
        });
    });
</script>
