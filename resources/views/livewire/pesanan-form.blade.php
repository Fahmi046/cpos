<div x-data
    @keydown.window="
        if ($event.key === 'F10') {
            $event.preventDefault();
            $el.querySelector('form').dispatchEvent(new Event('submit', {cancelable: true, bubbles: true}));
        }
    ">
    <div class="bg-white rounded-md shadow p-4 mb-4">
        <h2 class="text-base font-semibold text-gray-700 mb-3">Form Permintaan</h2>

        <form wire:submit.prevent="save" x-data x-init="$nextTick(() => $refs.tanggal.focus())" x-on:focus-tanggal.window="$refs.tanggal.focus()"
            x-on:focus-row.window="
                $nextTick(() => {
                    const el = $refs['obat_id_' + $event.detail.index];
                    if (el) el.focus();
                })
            "
            class="space-y-3 text-sm">

            <!-- Baris 1: No SP & Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block mb-1 font-medium">No SP</label>
                    <input type="text" wire:model="no_sp"
                        class="w-full rounded border-gray-200 bg-gray-100 text-gray-600 px-2 py-1 cursor-not-allowed"
                        readonly>
                </div>
                <div>
                    <label class="block mb-1 font-medium">Tanggal</label>
                    <input type="date" wire:model="tanggal" x-ref="tanggal"
                        @keydown.enter.prevent="$refs.kategori.focus()"
                        class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1" />
                </div>
            </div>

            <!-- Baris 2: Kategori -->
            <div>
                <label class="block mb-1 font-medium">Kategori</label>
                <select wire:model.live="kategori" id="kategori" x-ref="kategori"
                    @keydown.enter.prevent="$refs['obat_id_0'].focus()"
                    class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="OOT">OOT</option>
                    <option value="PREK">PREK</option>
                    <option value="PSIKO">PSIKO</option>
                    <option value="NARKO">NARKO</option>
                </select>
            </div>

            <!-- Detail Obat -->
            <h3 class="font-semibold text-gray-700 mt-4 mb-2">Detail Obat</h3>
            <div class="space-y-2">
                @foreach ($details as $index => $detail)
                    <div class="grid grid-cols-13 gap-2 items-center" wire:key="detail-{{ $index }}">
                        <!-- Nama Obat -->
                        <div class="relative col-span-4">
                            <input type="text"
                                class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1"
                                placeholder="Cari obat..." wire:model.defer="details.{{ $index }}.nama_obat"
                                wire:input="searchObat({{ $index }}, $event.target.value); resetHighlight({{ $index }})"
                                @focus="$wire.showObatDropdown[{{ $index }}] = true"
                                @keydown.arrow-down.prevent="$wire.incrementHighlight({{ $index }})"
                                @keydown.arrow-up.prevent="$wire.decrementHighlight({{ $index }})"
                                @keydown.enter.prevent="$wire.selectHighlightedObat({{ $index }}); $wire.showObatDropdown[{{ $index }}] = false; $refs['qty_{{ $index }}']?.focus();"
                                x-ref="obat_id_{{ $index }}">

                            @if (!empty($obatSearch[$index]) && ($showObatDropdown[$index] ?? false))
                                <div
                                    class="absolute z-10 w-full bg-white border rounded mt-1 shadow max-h-40 overflow-y-auto">
                                    @foreach ($obatSearch[$index] as $i => $obat)
                                        <div class="px-3 py-1 text-sm cursor-pointer {{ ($highlightedIndex[$index] ?? 0) === $i ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' }}"
                                            wire:click="selectObat({{ $index }}, {{ $obat->id }})">
                                            {{ $obat->nama_obat }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Qty -->
                        <div class="col-span-1">
                            <input type="number" wire:model.live="details.{{ $index }}.qty"
                                x-ref="qty_{{ $index }}"
                                @keydown.enter.prevent="$refs['isi_{{ $index }}']?.focus()"
                                class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1 text-center"
                                placeholder="Qty" min="0" step="1" />
                        </div>

                        <!-- Isi -->
                        <div class="col-span-1">
                            <input type="number" wire:model.live="details.{{ $index }}.isi_obat"
                                x-ref="isi_{{ $index }}"
                                @keydown.enter.prevent="$refs['checkbox_{{ $index }}']?.focus()"
                                class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-2 py-1 text-center"
                                placeholder="Isi" />
                        </div>

                        <!-- Harga -->
                        <div class="col-span-2">
                            <input type="number" wire:model="details.{{ $index }}.harga"
                                class="w-full rounded border-gray-200 bg-gray-100 px-2 py-1 text-right" readonly />
                        </div>

                        <!-- Jumlah -->
                        <div class="col-span-2">
                            <input type="number" wire:model="details.{{ $index }}.jumlah"
                                class="w-full rounded border-gray-200 bg-gray-100 px-2 py-1 text-right" readonly />
                        </div>

                        <!-- Satuan -->
                        <div class="col-span-1">
                            <input type="text" wire:model="details.{{ $index }}.satuan"
                                class="w-full rounded border-gray-200 bg-gray-100 px-2 py-1 text-center" readonly />
                        </div>

                        <!-- Utuh -->
                        <div class="flex items-center gap-1">
                            <input type="checkbox" wire:model="details.{{ $index }}.utuh_satuan"
                                x-ref="checkbox_{{ $index }}" @keydown.enter.prevent="$refs.addDetail?.focus()"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            <span class="text-xs">Utuh</span>
                        </div>

                        <!-- Hapus -->
                        <div class="col-span-1 flex justify-center">
                            <button type="button" wire:click="removeDetail({{ $index }})"
                                class="px-3 py-1 rounded text-white bg-red-500 hover:bg-red-600 text-sm">X</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tombol Tambah -->
            <button type="button" wire:click="addDetail" x-ref="addDetail"
                class="w-full md:w-auto px-4 py-1.5 rounded text-white bg-blue-600 hover:bg-blue-700 text-sm">
                + Tambah Obat
            </button>

            <!-- Tombol Simpan -->
            <div class="flex gap-2">
                <button type="submit"
                    class="w-full md:w-auto px-4 py-1.5 rounded text-white bg-green-600 hover:bg-green-700 text-sm">
                    Simpan (F10)
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
