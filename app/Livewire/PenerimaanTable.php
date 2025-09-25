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
        $penerimaanList = Penerimaan::with(['pesanan', 'kreditur'])
            ->where(function ($query) {
                $query->where('no_penerimaan', 'like', '%' . $this->search . '%')
                    ->orWhere('tanggal', 'like', '%' . $this->search . '%')
                    ->orWhereHas('pesanan', function ($q) {
                        $q->where('no_sp', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('kreditur', function ($q) {
                        $q->where('nama', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest('tanggal')
            ->paginate(10);

        return view('livewire.penerimaan-table', [
            'penerimaanList' => $penerimaanList
        ]);
    }

    public function mount()
    {
        $this->loadData();
    }

    public $penerimaans;


    public function loadData()
    {
        $this->penerimaans = Penerimaan::orderBy('no_penerimaan')->get();
    }


    public function delete($id)
    {
        $penerimaan = Penerimaan::find($id);

        if ($penerimaan) {
            // update tanggal ke hari ini
            $penerimaan->tanggal = now();
            $penerimaan->save();

            // hapus detail & penerimaan
            $penerimaan->details()->delete();
            $penerimaan->delete();

            $this->loadData();

            session()->flash('message', 'Data penerimaan berhasil dihapus.');
            $this->dispatch('refreshKodepenerimaan');
            $this->dispatch('focus-tanggal');
        }
    }
}
