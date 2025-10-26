<?php

namespace App\Livewire;

use id;
use App\Models\Obat;
use Livewire\Component;
use App\Models\Keranjang;
use App\Models\StokOutlet;
use App\Models\KategoriHarga;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KeranjangForm extends Component
{
    public $obat_id;
    public $kategori_harga_id;
    public $qty = 1;

    public $batch, $ed, $stok_akhir;


    public $keranjangItems = [];

    public function mount()
    {
        $this->loadKeranjang();
    }

    public function render()
    {
        $obats = Obat::all();
        $kategoriHargas = KategoriHarga::all();

        return view('livewire.keranjang-form', compact('obats', 'kategoriHargas'));
    }

    public function addToKeranjang()
    {
        $this->validate([
            'obat_id' => 'required|exists:obat,id',
            'qty' => 'required|integer|min:1',
        ]);

        $obat = Obat::findOrFail($this->obat_id);

        $harga = $obat->harga_jual; // pastikan ada kolom harga_jual di tabel obat

        if ($this->kategori_harga_id) {
            $kategori = KategoriHarga::find($this->kategori_harga_id);
            if ($kategori) {
                if ($kategori->tipe == 'diskon') {
                    $harga -= $harga * $kategori->persentase / 100;
                } else {
                    $harga += $harga * $kategori->persentase / 100;
                }
            }
        }

        Keranjang::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'obat_id' => $this->obat_id,
                'kategori_harga_id' => $this->kategori_harga_id,
            ],
            [
                'qty' => \DB::raw('qty + ' . $this->qty),
                'harga' => $harga,
                'subtotal' => $harga * $this->qty,
            ]
        );

        $this->reset(['obat_id', 'kategori_harga_id', 'qty']);
        $this->loadKeranjang();
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

    public $nama_obat = '';
    public $obatSearch = [];
    public $showObatDropdown = false;
    public $highlightedIndex = 0;
    public function searchObat($query)
    {
        $outletId = Auth::user()->outlet_id ?? 1;

        $this->obatSearch = StokOutlet::with('obat')
            ->where('outlet_id', $outletId)
            ->whereHas('obat', function ($q) use ($query) {
                $q->where('nama_obat', 'like', "%{$query}%");
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(100) // bisa disesuaikan
            ->get()
            ->groupBy(function ($item) {
                // Kelompokkan berdasarkan kombinasi obat-batch-ED
                $batch = $item->batch ?: '-';
                $ed = $item->ed ?: '-';
                return $item->obat_id . '-' . $batch . '-' . $ed;
            })
            ->map(function ($group) {
                // Ambil catatan terbaru dari kelompok
                $latest = $group->sortByDesc('tanggal')->sortByDesc('id')->first();

                // Hitung stok akhir manual: total masuk - total keluar
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
            ->values(); // reset index agar rapi
    }

    public function selectObat($id)
    {
        $stok = \App\Models\StokOutlet::with('obat')
            ->where('obat_id', $id)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();

        if ($stok && $stok->obat) {
            $this->obat_id = $stok->obat->id;

            // Format ED ke d/m/Y jika tidak null dan bukan '-'
            $edFormatted = $stok->ed && $stok->ed !== '-'
                ? \Carbon\Carbon::parse($stok->ed)->format('m/Y')
                : '-';

            // Format nama obat di input
            $this->nama_obat = "{$stok->obat->nama_obat} (Batch: {$stok->batch} | ED: {$edFormatted})";

            // Simpan data lain jika ingin digunakan
            $this->batch = $stok->batch ?? '-';
            $this->ed = $edFormatted;
            $this->stok_akhir = $stok->stok_akhir ?? 0;
        }

        $this->showObatDropdown = false;
    }


    public function incrementHighlight()
    {
        if ($this->highlightedIndex < count($this->obatSearch) - 1) {
            $this->highlightedIndex++;
        }
    }

    public function decrementHighlight()
    {
        if ($this->highlightedIndex > 0) {
            $this->highlightedIndex--;
        }
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
}
