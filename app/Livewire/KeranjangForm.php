<?php

namespace App\Livewire;

use App\Models\Obat;
use App\Models\Keranjang;
use App\Models\StokOutlet;
use App\Models\KategoriHarga;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KeranjangForm extends Component
{
    public $obat_id;
    public $nama_obat = '';
    public $obatSearch = [];
    public $showObatDropdown = false;
    public $highlightedIndex = 0;

    public $kategori_harga_id;
    public $nilai_persen = 0;
    public $harga = 0;
    public $harga_tampil = '0';

    public $qty = 1;
    public $subtotal = 0;
    public $subtotal_tampil = '0';

    public $batch, $ed, $stok_akhir, $persen_kategori;

    public $keranjangItems = [];

    public function mount()
    {
        $this->loadKeranjang();

        $normal = \App\Models\KategoriHarga::where('nama', 'Normal')->first();
        if ($normal) {
            $this->kategori_harga_id = $normal->id;
            $this->nilai_persen = $normal->persentase ?? 0;
        }

        // Fokus otomatis ke input nama_obat saat halaman pertama kali dibuka
        $this->dispatch('focus-nama-obat');
    }


    public function render()
    {
        $kategoriHargas = KategoriHarga::all();
        return view('livewire.keranjang-form', compact('kategoriHargas'));
    }

    /** -----------------------------
     *  🔹 KATEGORI & HARGA
     *  ----------------------------- */
    public function updatePersenKategori()
    {
        $kategori = KategoriHarga::find($this->kategori_harga_id);
        $this->nilai_persen = $kategori->persentase ?? 0;
        $this->updateHargaKategori();
    }

    public function updateHargaKategori()
    {
        if (!$this->obat_id || !$this->kategori_harga_id) return;

        $kategori = KategoriHarga::find($this->kategori_harga_id);
        $stok = StokOutlet::with('obat')
            ->where('obat_id', $this->obat_id)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();

        if (!$kategori || !$stok || !$stok->obat) return;

        $this->persen_kategori = $kategori->persentase ?? 0;
        $hargaDasar = $stok->obat->harga_beli ?? 0;
        $tipe = strtoupper($kategori->tipe ?? '');

        if ($tipe === 'NAIK') {
            $this->harga = $hargaDasar + ($hargaDasar * ($this->persen_kategori / 100));
        } elseif ($tipe === 'TURUN') {
            $this->harga = $hargaDasar - ($hargaDasar * ($this->persen_kategori / 100));
        } else {
            $this->harga = $hargaDasar;
        }

        $this->harga = round($this->harga);
        $this->harga_tampil = number_format($this->harga, 0, ',', '.');

        $this->hitungSubtotal();
    }

    /** -----------------------------
     *  🔹 SUBTOTAL
     *  ----------------------------- */
    public function updatedQty()
    {
        $this->hitungSubtotal();
    }

    public function hitungSubtotal()
    {
        $this->subtotal = $this->harga * $this->qty;
        $this->subtotal_tampil = number_format($this->subtotal, 0, ',', '.');
    }

    /** -----------------------------
     *  🔹 OBAT SEARCH & SELECT
     *  ----------------------------- */
    public function searchObat($query)
    {
        $outletId = Auth::user()->outlet_id ?? 1;

        $this->obatSearch = StokOutlet::with('obat')
            ->where('outlet_id', $outletId)
            ->whereHas('obat', fn($q) => $q->where('nama_obat', 'like', "%{$query}%"))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->groupBy(fn($item) => $item->obat_id . '-' . ($item->batch ?: '-') . '-' . ($item->ed ?: '-'))
            ->map(function ($group) {
                $latest = $group->sortByDesc('tanggal')->sortByDesc('id')->first();
                $totalMasuk = $group->sum('masuk');
                $totalKeluar = $group->sum('keluar');
                $stokAkhir = $totalMasuk - $totalKeluar;

                return (object)[
                    'id' => $latest->obat->id,
                    'nama' => $latest->obat->nama_obat,
                    'batch' => $latest->batch ?: '-',
                    'ed' => $latest->ed ?: '-',
                    'stok_akhir' => $stokAkhir,
                    'tanggal_update' => $latest->tanggal,
                ];
            })
            ->values();

        $this->showObatDropdown = true;
    }

    public function selectObat($id)
    {
        $stok = StokOutlet::with('obat')
            ->where('obat_id', $id)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();

        if ($stok && $stok->obat) {
            $this->obat_id = $stok->obat->id;
            $this->stok_akhir = $stok->stok_akhir ?? 0;
            $this->batch = $stok->batch ?? '-';
            $this->ed = $stok->ed ? \Carbon\Carbon::parse($stok->ed)->format('m/Y') : '-';
            $this->nama_obat = "{$stok->obat->nama_obat} (Batch: {$stok->batch} | ED: {$this->ed})";

            $this->updateHargaKategori();
        }

        $this->showObatDropdown = false;
        $this->resetHighlight();
    }

    public function incrementHighlight()
    {
        if ($this->highlightedIndex < count($this->obatSearch) - 1) $this->highlightedIndex++;
    }

    public function decrementHighlight()
    {
        if ($this->highlightedIndex > 0) $this->highlightedIndex--;
    }

    public function selectHighlightedObat()
    {
        if (!empty($this->obatSearch[$this->highlightedIndex])) {
            $this->selectObat($this->obatSearch[$this->highlightedIndex]->id);
        }
    }

    public function resetHighlight()
    {
        $this->highlightedIndex = 0;
    }

    /** -----------------------------
     *  🔹 KERANJANG
     *  ----------------------------- */
    public function addToKeranjang()
    {
        $this->validate([
            'obat_id' => 'required|exists:obat,id',
            'qty' => 'required|integer|min:1',
        ]);

        $harga = $this->harga;

        \App\Models\Keranjang::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'obat_id' => $this->obat_id,
                'kategori_harga_id' => $this->kategori_harga_id,
            ],
            [
                'qty' => $this->qty,
                'harga' => $harga,
                'subtotal' => $harga * $this->qty,
            ]
        );

        $this->reset(['obat_id', 'nama_obat', 'qty', 'harga', 'harga_tampil', 'subtotal', 'subtotal_tampil']);
        $this->qty = 1;
        $this->loadKeranjang();

        // Fokus kembali ke kolom nama obat
        $this->dispatch('focus-nama-obat');
    }

    public function removeItem($id)
    {
        Keranjang::where('id', $id)->delete();
        $this->loadKeranjang();
    }

    private function loadKeranjang()
    {
        $this->keranjangItems = Keranjang::with('obat', 'kategoriHarga')
            ->where('user_id', Auth::id())
            ->get();
    }

    public function getTotalProperty()
    {
        return $this->keranjangItems->sum('subtotal');
    }
}
