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

    protected $listeners = ['refreshTable' => '$refresh'];

    // Reset halaman ketika pencarian berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $krediturs = Kreditur::query()
            ->where('nama', 'like', "%{$this->search}%")
            ->orderBy('nama')
            ->paginate(10);

        return view('livewire.kreditur-table', compact('krediturs'));
    }

    public function delete($id)
    {
        Kreditur::findOrFail($id)->delete();

        session()->flash('message', 'Kreditur berhasil dihapus.');

        // Trigger event agar form tambah/edit update otomatis
        $this->dispatch('refreshKodeKreditur');
        $this->dispatch('focus-nama');
    }
}
