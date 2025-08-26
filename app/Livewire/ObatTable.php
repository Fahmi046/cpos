<?php

namespace App\Livewire;

use App\Models\Obat;
use Livewire\Component;
use Livewire\WithPagination;

class ObatTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    protected $updatesQueryString = ['search'];

    protected $listeners = ['refreshTable' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Obat::findOrFail($id)->delete();
        session()->flash('message', 'Obat berhasil dihapus.');
        $this->dispatch('refreshKodeObat');
        $this->dispatch('focus-nama-obat');
    }

    public function render()
    {
        $obats = Obat::with(['kategori', 'sediaan', 'komposisi', 'satuan', 'pabrik'])
            ->where(function ($q) {
                $q->where('kode_obat', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_obat', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.obat-table', [
            'obats' => $obats,
        ]);
    }
}
