<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Penerimaan;

class PenerimaanTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshTable' => 'loadData'];

    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $penerimaan = Penerimaan::with('pesanan', 'kreditur')
            ->where('no_penerimaan', 'like', "%{$this->search}%")
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('livewire.penerimaan-table', compact('penerimaan'));
    }
    public $penerimaans;


    public function loadData()
    {
        $this->penerimaans = penerimaan::orderBy('no_penerimaan')->get();
    }


    public function delete($id)
    {
        $penerimaan = Penerimaan::find($id);

        if ($penerimaan) {
            // hapus relasi detail dulu biar aman
            $penerimaan->details()->delete();
            $penerimaan->delete();
            $this->loadData();

            session()->flash('message', 'Data penerimaan berhasil dihapus.');

            $this->dispatch('focus-tanggal');
        }
    }
}
