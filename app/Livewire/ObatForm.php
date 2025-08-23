<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Obat;

class ObatForm extends Component
{
    public $obat_id;
    public $kode_obat, $nama_obat, $kategori, $bentuk_sediaan, $kandungan;
    public $harga_beli, $harga_jual, $stok, $satuan, $pabrik, $tgl_expired;

    private function generateKodeObat()
    {
        // Ambil kode terakhir
        $last = Obat::orderBy('id', 'desc')->first();

        // Nomor urut
        $nextNumber = $last ? ((int) substr($last->kode_obat, 4)) + 1 : 1;

        // Format: 0010 + nomor urut 4 digit
        return '0010' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // ❌ jangan dobel deklarasi
    protected $listeners = [
        'edit-obat'   => 'edit',
        'refreshForm' => '$refresh',
    ];

    public function render()
    {
        return view('livewire.obat-form');
    }

    public function store()
    {
        $this->validate([
            'nama_obat'  => 'required|string|max:150',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $data = $this->only([
            'nama_obat',
            'kategori',
            'bentuk_sediaan',
            'kandungan',
            'harga_beli',
            'harga_jual',
            'stok',
            'satuan',
            'pabrik',
            'tgl_expired',
        ]);

        if ($this->obat_id) {
            // Update data lama
            $obat = Obat::findOrFail($this->obat_id);
            $obat->update($data);
            session()->flash('message', 'Data berhasil diperbarui.');
        } else {
            // Generate kode obat otomatis
            $data['kode_obat'] = $this->generateKodeObat();

            Obat::create($data);
            session()->flash('message', 'Data berhasil ditambahkan.');
        }

        $this->resetForm();
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
