<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pabrik;

class PabrikTable extends Component
{
    protected $listeners = ['refreshTable' => 'loadData'];

    public function delete($id)
    {
        Pabrik::findOrFail($id)->delete();
        $this->loadData();
        session()->flash('message', 'Pabrik berhasil dihapus.');

        $this->dispatch('refreshKodePabrik');
        $this->dispatch('focus-nama-pabrik');
    }

    public function render()
    {
        $pabriks = Pabrik::orderBy('nama_pabrik')->get();
        return view('livewire.pabrik-table', compact('pabriks'));
    }


    public function mount()
    {
        $this->loadData();
    }

    public $pabriks;
    public function loadData()
    {
        $this->pabriks = Pabrik::orderBy('nama_pabrik')->get();
    }
}
