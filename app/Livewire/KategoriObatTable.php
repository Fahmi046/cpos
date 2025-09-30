<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KategoriObat;
use Livewire\WithPagination;

class KategoriObatTable extends Component
{
    protected $listeners = ['kategori-updated' => '$loadData'];

    use WithPagination;

    // opsional: atur theme pagination (default pakai Tailwind)
    protected string $paginationTheme = 'tailwind';


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
            'kategoriObat' => \App\Models\KategoriObat::latest()->paginate(10),
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
