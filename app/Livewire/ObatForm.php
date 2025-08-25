<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Obat;
use App\Models\KategoriObat;
use App\Models\SatuanObat;
use App\Models\BentukSediaan;
use App\Models\Pabrik;


class ObatForm extends Component
{
    public $obat_id;
    public $kode_obat, $nama_obat, $kandungan;
    public $kategori_id, $satuan_id, $sediaan_id, $pabrik_id;
    public $harga_beli, $harga_jual, $satuan, $pabrik;

    public function mount()
    {
        // Saat pertama kali buka halaman, generate kode obat baru
        $this->generateKodeObat();
    }
    public function generateKodeObat()
    {
        $last = Obat::orderBy('id', 'desc')->first();

        if ($last && $last->kode_obat) {
            // Ambil hanya angka setelah prefix "0010"
            $lastNumber = intval(substr($last->kode_obat, 4));
            $nextNumber = $lastNumber + 1;
        } else {
            // Kalau belum ada data → mulai dari 1
            $nextNumber = 1;
        }

        // Format hasilnya
        $this->kode_obat = '0010' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    protected $listeners = [
        'edit-obat'   => 'edit',
        'refreshForm' => '$refresh',
        'refreshKodeObat' => 'generateKodeObat',
    ];


    public function render()
    {
        return view('livewire.obat-form', [
            'kategoriList' => KategoriObat::all(),
            'satuanList' => SatuanObat::all(),
            'sediaanList' => BentukSediaan::all(),
            'pabrikList' => Pabrik::all(),
        ]);
    }

    public function store()
    {
        $this->validate([
            'nama_obat' => 'required|string|max:150',
            'harga_jual' => 'required|numeric|min:0',
            'kategori_id' => 'required',
            'satuan_id' => 'required',
            'sediaan_id' => 'required',
            'pabrik_id' => 'required',
        ]);

        // ✅ pastikan kode_obat di-generate sebelum simpan
        if (!$this->obat_id && !$this->kode_obat) {
            $this->generateKodeObat();
        }

        if ($this->obat_id) {
            $obat = Obat::findOrFail($this->obat_id);
            $obat->update([
                'kode_obat'    => $this->kode_obat,
                'nama_obat'    => $this->nama_obat,
                'kategori_id'  => $this->kategori_id,   // ✅ pakai _id
                'sediaan_id'   => $this->sediaan_id,
                'kandungan'    => $this->kandungan,
                'harga_beli'   => $this->harga_beli,
                'harga_jual'   => $this->harga_jual,
                'satuan_id'    => $this->satuan_id,
                'pabrik_id'    => $this->pabrik_id,
            ]);
            session()->flash('message', 'Data berhasil diperbarui.');
        } else {
            Obat::create([
                'kode_obat'    => $this->kode_obat,
                'nama_obat'    => $this->nama_obat,
                'kategori_id'  => $this->kategori_id,   // ✅ pakai _id
                'sediaan_id'   => $this->sediaan_id,
                'kandungan'    => $this->kandungan,
                'harga_beli'   => $this->harga_beli,
                'harga_jual'   => $this->harga_jual,
                'satuan_id'    => $this->satuan_id,
                'pabrik_id'    => $this->pabrik_id,
            ]);
            session()->flash('message', 'Data berhasil ditambahkan.');
        }

        $this->resetForm();
        $this->generateKodeObat(); // ✅ setelah reset, buat lagi kode baru untuk input berikutnya
        $this->dispatch('focus-nama-obat');  // 🔥 trigger event fokus
        $this->dispatch('refreshTable');
    }


    public function edit($id)
    {
        $obat = Obat::findOrFail($id);
        $this->fill($obat->toArray());
        $this->obat_id = $id;
    }

    public function resetForm()
    {
        $this->reset([
            'obat_id',
            'nama_obat',
            'kategori_id',
            'sediaan_id',
            'kandungan',
            'harga_beli',
            'harga_jual',
            'satuan_id',
            'pabrik_id',
        ]);

        // Generate kode obat baru
        $this->kode_obat = $this->generateKodeObat();
    }
    public $searchKategori = '';
    public $kategoriList = [];
    public $highlightIndex = 0;

    public function updatedSearchKategori()
    {
        $this->kategoriList = KategoriObat::where('nama_kategori', 'like', '%' . $this->searchKategori . '%')
            ->limit(10)
            ->get();

        $this->resetHighlight();
    }

    public function resetHighlight()
    {
        $this->highlightIndex = 0;
    }

    public function incrementHighlight()
    {
        if ($this->highlightIndex === count($this->kategoriList) - 1) {
            $this->highlightIndex = 0;
        } else {
            $this->highlightIndex++;
        }
    }

    public function decrementHighlight()
    {
        if ($this->highlightIndex === 0) {
            $this->highlightIndex = count($this->kategoriList) - 1;
        } else {
            $this->highlightIndex--;
        }
    }

    public function pilihHighlight()
    {
        $kategori = $this->kategoriList[$this->highlightIndex] ?? null;
        if ($kategori) {
            $this->pilihKategori($kategori['id'], $kategori['nama_kategori']);
        }
    }

    public function pilihKategori($id, $nama)
    {
        $this->kategori_id = $id;
        $this->searchKategori = $nama;
        $this->kategoriList = [];
    }


    public $searchsediaan = '';
    public $sediaanList = [];

    public function updatedSearchsediaan()
    {
        $this->sediaanList = BentukSediaan::where('nama_sediaan', 'like', '%' . $this->searchsediaan . '%')
            ->limit(10)
            ->get();

        $this->resetHighlight();
    }


    public function incrementHighlightsediaan()
    {
        if ($this->highlightIndex === count($this->sediaanList) - 1) {
            $this->highlightIndex = 0;
        } else {
            $this->highlightIndex++;
        }
    }

    public function decrementHighlightsediaan()
    {
        if ($this->highlightIndex === 0) {
            $this->highlightIndex = count($this->sediaanList) - 1;
        } else {
            $this->highlightIndex--;
        }
    }

    public function pilihHighlightsediaan()
    {
        $sediaan = $this->sediaanList[$this->highlightIndex] ?? null;
        if ($sediaan) {
            $this->pilihsediaan($sediaan['id'], $sediaan['nama_sediaan']);
        }
    }

    public function pilihsediaan($id, $nama)
    {
        $this->sediaan_id = $id;
        $this->searchsediaan = $nama;
        $this->sediaanList = [];
    }


    public $searchpabrik = '';
    public $pabrikList = [];

    public function updatedSearchpabrik()
    {
        $this->pabrikList = Pabrik::where('nama_pabrik', 'like', '%' . $this->searchpabrik . '%')
            ->limit(10)
            ->get();

        $this->resetHighlight();
    }


    public function incrementHighlightpabrik()
    {
        if ($this->highlightIndex === count($this->pabrikList) - 1) {
            $this->highlightIndex = 0;
        } else {
            $this->highlightIndex++;
        }
    }

    public function decrementHighlightpabrik()
    {
        if ($this->highlightIndex === 0) {
            $this->highlightIndex = count($this->pabrikList) - 1;
        } else {
            $this->highlightIndex--;
        }
    }

    public function pilihHighlightpabrik()
    {
        $pabrik = $this->pabrikList[$this->highlightIndex] ?? null;
        if ($pabrik) {
            $this->pilihpabrik($pabrik['id'], $pabrik['nama_pabrik']);
        }
    }

    public function pilihpabrik($id, $nama)
    {
        $this->pabrik_id = $id;
        $this->searchpabrik = $nama;
        $this->pabrikList = [];
    }


    public $searchsatuan = '';
    public $satuanList = [];

    public function updatedSearchsatuan()
    {
        $this->satuanList = SatuanObat::where('nama_satuan', 'like', '%' . $this->searchsatuan . '%')
            ->limit(10)
            ->get();

        $this->resetHighlight();
    }


    public function incrementHighlightsatuan()
    {
        if ($this->highlightIndex === count($this->satuanList) - 1) {
            $this->highlightIndex = 0;
        } else {
            $this->highlightIndex++;
        }
    }

    public function decrementHighlightsatuan()
    {
        if ($this->highlightIndex === 0) {
            $this->highlightIndex = count($this->satuanList) - 1;
        } else {
            $this->highlightIndex--;
        }
    }

    public function pilihHighlightsatuan()
    {
        $satuan = $this->satuanList[$this->highlightIndex] ?? null;
        if ($satuan) {
            $this->pilihsatuan($satuan['id'], $satuan['nama_satuan']);
        }
    }

    public function pilihsatuan($id, $nama)
    {
        $this->satuan_id = $id;
        $this->searchsatuan = $nama;
        $this->satuanList = [];
    }
}
