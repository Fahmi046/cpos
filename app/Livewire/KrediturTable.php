<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kreditur;

class KrediturTable extends Component
{
    use WithPagination;

    public $search = '';

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected $listeners = ['refreshTable' => '$refresh'];

    public function render()
    {
        $krediturs = Kreditur::where('nama', 'like', "%{$this->search}%")
            ->orderBy('nama')
            ->paginate(10);

        return view('livewire.kreditur-table', [
            'krediturs' => $krediturs,
        ]);
    }

    public function delete($id)
    {
        Kreditur::findOrFail($id)->delete();

        session()->flash('message', 'Kreditur berhasil dihapus.');

        $this->dispatch('refreshKodeKreditur');
        $this->dispatch('focus-nama');
    }
}
