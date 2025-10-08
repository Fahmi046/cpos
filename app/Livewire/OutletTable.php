<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Outlet;

class OutletTable extends Component
{
    public $outlets;

    protected $listeners = ['refreshOutletTable' => '$refresh'];

    public function render()
    {
        $this->outlets = Outlet::latest()->get();
        return view('livewire.outlet-table');
    }

    public function edit($id)
    {
        $this->dispatch('editOutlet', $id);
    }

    public function delete($id)
    {
        Outlet::findOrFail($id)->delete();
        session()->flash('message', 'Outlet berhasil dihapus');
        $this->dispatch('refreshOutletTable');
    }
}
