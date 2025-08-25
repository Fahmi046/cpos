<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Obat;

class ObatTable extends Component
{
    public $obats;

    // dengarkan event dari ObatForm
    protected $listeners = ['refreshTable' => 'loadObats'];


    public function mount()
    {
        $this->loadObats();
    }

    public function loadObats()
    {
        $this->obats = Obat::orderBy('id', 'desc')->get();
    }

    public function delete($id)
    {
        Obat::findOrFail($id)->delete();
        $this->loadObats();
        session()->flash('message', 'Obat berhasil dihapus.');
        // Corrected: Dispatch event using the new syntax
        $this->dispatch('refreshKodeObat');
        $this->dispatch('focus-nama-obat');
    }

    public function render()
    {
        $obats = obat::orderBy('nama_obat')->get();
        return view('livewire.obat-table', compact('obats'));
    }
}
