<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pesanan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PesananExport;

class PesananTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshTable' => 'loadData'];

    public $search = '';
    protected $paginationTheme = 'tailwind';

    // Reset halaman ketika search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Edit pesanan (dispatch ke browser)
    public function edit($id)
    {
        $this->dispatch('edit-pesanan', ['id' => $id]);
    }

    // Hapus pesanan beserta detailnya
    public function delete($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->details()->delete();
        $pesanan->delete();
        $this->loadData();
        session()->flash('message', 'Pabrik berhasil dihapus.');

        $this->dispatch('refreshKodepesanan');

        $this->dispatch('focus-tanggal');
    }

    // Export Excel dengan filter search
    public function exportExcel()
    {
        return Excel::download(new PesananExport($this->search), 'pesanan.xlsx');
    }

    public function render()
    {
        $pesananList = Pesanan::with('details.obat')
            ->where(function ($query) {
                $query->where('no_sp', 'like', '%' . $this->search . '%')
                    ->orWhere('tanggal', 'like', '%' . $this->search . '%')
                    ->orWhereHas('details.obat', function ($q) {
                        $q->where('nama_obat', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(5);

        return view('livewire.pesanan-table', [
            'pesananList' => $pesananList
        ]);
    }

    public function mount()
    {
        $this->loadData();
    }
    public $pesanans;

    public function loadData()
    {
        $this->pesanans = Pesanan::orderBy('no_sp')->get();
    }
}
