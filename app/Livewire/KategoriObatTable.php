<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KategoriObat;

class KategoriObatTable extends Component
{
    protected $listeners = ['kategori-updated' => '$loadData'];

    public function delete($id)
    {
        KategoriObat::findOrFail($id)->delete();

        session()->flash('message', 'kategori berhasil dihapus.');

        $this->dispatch('refreshKodeKategori');
        $this->dispatch('focus-nama-kategori');
    }

    public function render()
    {
        return view('livewire.kategori-obat-table', [
            'kategoriObat' => KategoriObat::latest()->get(),
        ]);
    }

    public function mount()
    {
        $this->loadData();
    }

    public $kategoris;
    public function loadData()
    {
        $this->kategoris = KategoriObat::orderBy('nama_kategori')->get();
    }
}
