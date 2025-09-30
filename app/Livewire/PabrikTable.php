<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pabrik;

class PabrikTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshTable' => 'loadData'];

    public function delete($id)
    {
        Pabrik::findOrFail($id)->delete();
        session()->flash('message', 'Pabrik berhasil dihapus.');

        $this->dispatch('refreshKodePabrik');
        $this->dispatch('focus-nama-pabrik');

        // refresh otomatis ke halaman pertama biar data update
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.pabrik-table', [
            'pabriks' => Pabrik::orderBy('nama_pabrik')->paginate(10), // ✅ paginate, bukan get
        ]);
    }
}
