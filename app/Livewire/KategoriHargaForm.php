<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KategoriHarga;

class KategoriHargaForm extends Component
{
    public $kategori_id;
    public $nama;
    public $persentase;
    public $tipe;

    protected $listeners = ['editKategori' => 'loadData'];

    protected $rules = [
        'nama' => 'required|string|max:255',
        'persentase' => 'nullable|numeric|min:0',
        'tipe' => 'required|string|max:100',
    ];

    public function render()
    {
        return view('livewire.kategori-harga-form');
    }

    public function save()
    {
        $this->validate();

        KategoriHarga::updateOrCreate(
            ['id' => $this->kategori_id],
            [
                'nama' => $this->nama,
                'persentase' => $this->persentase,
                'tipe' => $this->tipe,
            ]
        );

        session()->flash('message', $this->kategori_id ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');

        $this->resetInput();
        $this->dispatch('kategoriDisimpan');
    }

    public function loadData($id)
    {
        $kategori = KategoriHarga::findOrFail($id);
        $this->kategori_id = $kategori->id;
        $this->nama = $kategori->nama;
        $this->persentase = $kategori->persentase;
        $this->tipe = $kategori->tipe;
    }

    public function resetInput()
    {
        $this->kategori_id = null;
        $this->nama = '';
        $this->persentase = '';
        $this->tipe = '';
    }
}
