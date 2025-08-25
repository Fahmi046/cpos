<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Komposisi;

class KomposisiTable extends Component
{
    protected $listeners = ['refreshTable' => 'loadData'];

    public function delete($id)
    {
        Komposisi::find($id)->delete();
        $this->loadData();
        session()->flash('message', 'Komposisi berhasil dihapus.');

        $this->dispatch('refreshKodeKomposisi');
        $this->dispatch('focus-nama-komposisi');
    }

    public function render()
    {
        $komposisis = Komposisi::orderBy('nama_komposisi')->get();
        return view('livewire.komposisi-table', compact('komposisis'));
    }


    public function mount()
    {
        $this->loadData();
    }

    public $komposisis;
    public function loadData()
    {
        $this->komposisis = Komposisi::orderBy('nama_komposisi')->get();
    }
}
