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
                <div class="grid grid-cols-7 gap-2 mb-2" wire:key="detail-{{ $index }}">
                    <!-- Nama Obat -->
                    <select wire:model.live="details.{{ $index }}.obat_id" x-ref="obat_id_{{ $index }}"
                        @keydown.enter.prevent="$refs['qty_{{ $index }}']?.focus()"
                        class="border rounded p-2 w-full">
                        <option value="">-- Pilih Obat --</option>
                        @foreach ($obatList as $obat)
                            <option value="{{ $obat->id }}">{{ $obat->nama_obat }}</option>
                        @endforeach
                    </select>

                    <!-- Qty -->
                    <input type="number" wire:model.live="details.{{ $index }}.qty"
                        x-ref="qty_{{ $index }}" @keydown.enter.prevent="$refs.addDetail?.focus()"
                        placeholder="Qty" class="border rounded p-2" />

                    <!-- Harga -->
                    <input type="number" wire:model="details.{{ $index }}.harga" placeholder="Harga"
                        class="border rounded p-2" readonly>

                    <!-- Isi Obat -->
                    <input type="number" wire:model="details.{{ $index }}.isi" placeholder="Isi"
                        class="border rounded p-2 bg-gray-100" readonly>

                    <!-- Satuan -->
                    <input type="text" wire:model="details.{{ $index }}.satuan" placeholder="Satuan"
                        class="border rounded p-2 bg-gray-100" readonly>

                    <!-- Jumlah -->
                    <input type="number" wire:model="details.{{ $index }}.jumlah" placeholder="Jumlah"
                        class="border rounded p-2 bg-gray-100" readonly>

                    <button type="button" wire:click="removeDetail({{ $index }})"
                        class="bg-red-500 text-white px-2 rounded">X</button>
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
