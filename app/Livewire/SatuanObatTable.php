<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SatuanObat;
use Livewire\WithPagination;

class SatuanObatTable extends Component
{
    protected $listeners = ['refreshTable' => '$refresh'];

    use WithPagination;

    public function delete($id)
    {
        SatuanObat::find($id)?->delete();
        session()->flash('message', 'Data berhasil dihapus.');

        $this->dispatch('resetForm');
        $this->dispatch('focusNama');
    }

    public function render()
    {
        return view('livewire.satuan-obat-table', [
            'satuans' => SatuanObat::orderBy('id', 'desc')->paginate(10), // tampil 10 per halaman
        ]);
    }
}
