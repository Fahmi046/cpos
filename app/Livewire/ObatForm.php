<?php

namespace App\Livewire;

use App\Models\Obat;
use App\Models\Pabrik;
use Livewire\Component;
use App\Models\Kreditur;
use App\Models\Komposisi;
use App\Models\SatuanObat;
use App\Models\KategoriObat;
use App\Models\BentukSediaan;


class ObatForm extends Component
{
    public $obat_id;
    public $kode_obat, $nama_obat;
    public $isi_obat, $dosis;
    public $kategori_id, $satuan_id, $sediaan_id, $komposisi_id, $kreditur_id, $pabrik_id;
    public $harga_beli, $harga_jual;

    public $prekursor = 0;
    public $psikotropika = 0;
    public $utuh_satuan = 0;
    public $resep_active = 0;
    public $aktif = 1; // default aktif


    public function mount($obat_id = null)
    {

        if ($obat_id) {
            $obat = Obat::findOrFail($obat_id);
            $this->fill($obat->toArray());
        }
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
            'krediturList' => Kreditur::all(),
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
            'kreditur_id' => 'required',
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
                'komposisi_id'    => $this->komposisi_id,
                'harga_beli'   => $this->harga_beli,
                'harga_jual'   => $this->harga_jual,
                'satuan_id'    => $this->satuan_id,
                'pabrik_id'    => $this->pabrik_id,
                'aktif'    => $this->aktif,
                'isi_obat'    => $this->isi_obat,
                'dosis'    => $this->dosis,
                'utuh_satuan'    => $this->utuh_satuan,
                'prekursor'    => $this->prekursor,
                'psikotropika'    => $this->psikotropika,
                'kreditur_id'    => $this->kreditur_id,
                'resep_active'    => $this->resep_active,
                'stok_awal'    => $this->stok_awal,
            ]);
            session()->flash('message', 'Data berhasil diperbarui.');
        } else {
            Obat::create([
                'kode_obat'    => $this->kode_obat,
                'nama_obat'    => $this->nama_obat,
                'kategori_id'  => $this->kategori_id,   // ✅ pakai _id
                'sediaan_id'   => $this->sediaan_id,
                'komposisi_id'    => $this->komposisi_id,
                'harga_beli' => str_replace(['.', ','], '', $this->harga_beli),
                'harga_jual' => str_replace(['.', ','], '', $this->harga_jual),
                'satuan_id'    => $this->satuan_id,
                'pabrik_id'    => $this->pabrik_id,
                'aktif'    => $this->aktif,
                'isi_obat'    => $this->isi_obat,
                'dosis'    => $this->dosis,
                'utuh_satuan'    => $this->utuh_satuan,
                'prekursor'    => $this->prekursor,
                'psikotropika'    => $this->psikotropika,
                'kreditur_id'    => $this->kreditur_id,
                'resep_active'    => $this->resep_active,
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
        $obat = Obat::with(['kategori', 'satuan', 'sediaan', 'pabrik', 'komposisi'])
            ->findOrFail($id);

        $this->fill($obat->toArray());
        $this->obat_id = $id;

        $this->searchKategori   = $obat->kategori->nama_kategori   ?? '';
        $this->kategori_id      = $obat->kategori_id;

        $this->searchsatuan     = $obat->satuan->nama_satuan       ?? '';
        $this->satuan_id        = $obat->satuan_id;

        $this->searchsediaan    = $obat->sediaan->nama_sediaan     ?? '';
        $this->sediaan_id       = $obat->sediaan_id;

        $this->searchpabrik     = $obat->pabrik->nama_pabrik       ?? '';
        $this->pabrik_id        = $obat->pabrik_id;

        $this->searchkomposisi  = $obat->komposisi->nama_komposisi ?? '';
        $this->komposisi_id     = $obat->komposisi_id;

        $this->searchkreditur   = $obat->kreditur->nama ?? '';
        $this->kreditur_id      = $obat->kreditur_id;

        // ✅ Konversi kolom boolean agar checkbox aktif
        $this->utuh_satuan   = (bool) $obat->utuh_satuan;
        $this->prekursor     = (bool) $obat->prekursor;
        $this->psikotropika  = (bool) $obat->psikotropika;
        $this->resep_active  = (bool) $obat->resep_active;
        $this->aktif         = (bool) $obat->aktif;
    }

    public function resetForm()
    {
        $this->reset([
            'obat_id',
            'nama_obat',
            'isi_obat',
            'dosis',
            'kategori_id',
            'sediaan_id',
            'komposisi_id',
            'harga_beli',
            'harga_jual',
            'satuan_id',
            'pabrik_id',
            'kreditur_id',
            // untuk input pencarian autocomplete
            'searchKategori',
            'searchsatuan',
            'searchsediaan',
            'searchpabrik',
            'searchkomposisi',
            'searchkreditur',
            'aktif',
            'utuh_satuan',
            'prekursor',
            'psikotropika',
            'resep_active',
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


    public $searchkomposisi = '';
    public $komposisiList = [];

    public function updatedSearchkomposisi()
    {
        $this->komposisiList = Komposisi::where('nama_komposisi', 'like', '%' . $this->searchkomposisi . '%')
            ->limit(10)
            ->get();

        $this->resetHighlight();
    }


    public function incrementHighlightkomposisi()
    {
        if ($this->highlightIndex === count($this->komposisiList) - 1) {
            $this->highlightIndex = 0;
        } else {
            $this->highlightIndex++;
        }
    }

    public function decrementHighlightkomposisi()
    {
        if ($this->highlightIndex === 0) {
            $this->highlightIndex = count($this->komposisiList) - 1;
        } else {
            $this->highlightIndex--;
        }
    }

    public function pilihHighlightkomposisi()
    {
        $komposisi = $this->komposisiList[$this->highlightIndex] ?? null;
        if ($komposisi) {
            $this->pilihkomposisi($komposisi['id'], $komposisi['nama_komposisi']);
        }
    }

    public function pilihkomposisi($id, $nama)
    {
        $this->komposisi_id = $id;
        $this->searchkomposisi = $nama;
        $this->komposisiList = [];
    }


    public $searchkreditur = '';
    public $krediturList = [];

    public function updatedSearchkreditur()
    {
        $this->krediturList = Kreditur::where('nama', 'like', '%' . $this->searchkreditur . '%')
            ->limit(10)
            ->get();

        $this->resetHighlight();
    }


    public function incrementHighlightKreditur()
    {
        if ($this->highlightIndex === count($this->krediturList) - 1) {
            $this->highlightIndex = 0;
        } else {
            $this->highlightIndex++;
        }
    }

    public function decrementHighlightKreditur()
    {
        if ($this->highlightIndex === 0) {
            $this->highlightIndex = count($this->krediturList) - 1;
        } else {
            $this->highlightIndex--;
        }
    }

    public function pilihHighlightKreditur()
    {
        $kreditur = $this->krediturList[$this->highlightIndex] ?? null;
        if ($kreditur) {
            $this->pilihkreditur($kreditur['id'], $kreditur['nama']);
        }
    }

    public function pilihkreditur($id, $nama)
    {
        $this->kreditur_id = $id;
        $this->searchkreditur = $nama;
        $this->krediturList = [];
    }
}
