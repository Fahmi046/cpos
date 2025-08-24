<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BentukSediaan;

class BentukSediaanTable extends Component
{
    public $sediaans;

    protected $listeners = ['refreshTable' => 'loadData'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->sediaans = BentukSediaan::orderBy('nama_sediaan')->get();
    }

    public function delete($id)
    {
        BentukSediaan::findOrFail($id)->delete();
        $this->loadData();
        session()->flash('message', 'Sediaan berhasil dihapus.');

        $this->dispatch('refreshKodeSediaan');
        $this->dispatch('focus-nama-sediaan');
    }

    public function render()
    {
        return view('livewire.bentuk-sediaan-table');
    }
}
