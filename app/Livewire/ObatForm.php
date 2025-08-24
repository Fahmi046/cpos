<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Obat;

class ObatForm extends Component
{
    public $obat_id;
    public $kode_obat, $nama_obat, $kategori, $bentuk_sediaan, $kandungan;
    public $harga_beli, $harga_jual, $stok, $satuan, $pabrik, $tgl_expired;

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
        return view('livewire.obat-form');
    }

    public function store()
    {
        $this->validate([
            'nama_obat' => 'required|string|max:150',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        // ✅ pastikan kode_obat di-generate sebelum simpan
        if (!$this->obat_id && !$this->kode_obat) {
            $this->generateKodeObat();
        }

        if ($this->obat_id) {
            $obat = Obat::findOrFail($this->obat_id);
            $obat->update([
                'kode_obat' => $this->kode_obat,
                'nama_obat' => $this->nama_obat,
                'kategori' => $this->kategori,
                'bentuk_sediaan' => $this->bentuk_sediaan,
                'kandungan' => $this->kandungan,
                'harga_beli' => $this->harga_beli,
                'harga_jual' => $this->harga_jual,
                'stok' => $this->stok,
                'satuan' => $this->satuan,
                'pabrik' => $this->pabrik,
                'tgl_expired' => $this->tgl_expired,
            ]);
            session()->flash('message', 'Data berhasil diperbarui.');
        } else {
            Obat::create([
                'kode_obat' => $this->kode_obat,
                'nama_obat' => $this->nama_obat,
                'kategori' => $this->kategori,
                'bentuk_sediaan' => $this->bentuk_sediaan,
                'kandungan' => $this->kandungan,
                'harga_beli' => $this->harga_beli,
                'harga_jual' => $this->harga_jual,
                'stok' => $this->stok,
                'satuan' => $this->satuan,
                'pabrik' => $this->pabrik,
                'tgl_expired' => $this->tgl_expired,
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
            'kategori',
            'bentuk_sediaan',
            'kandungan',
            'harga_beli',
            'harga_jual',
            'stok',
            'satuan',
            'pabrik',
            'tgl_expired'
        ]);

        // Generate kode obat baru
        $this->kode_obat = $this->generateKodeObat();
    }
}
