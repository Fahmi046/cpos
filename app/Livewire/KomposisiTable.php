<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Komposisi;

class KomposisiTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshTable' => '$refresh'];

    public function delete($id)
    {
        Komposisi::findOrFail($id)->delete();

        session()->flash('message', 'Komposisi berhasil dihapus.');

        $this->dispatch('refreshKodeKomposisi');
        $this->dispatch('focus-nama-komposisi');
    }

    public function render()
    {
        $komposisis = Komposisi::orderBy('nama_komposisi')->paginate(10);

        return view('livewire.komposisi-table', compact('komposisis'));
    }
}
