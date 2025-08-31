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

    public $search = '';
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $this->dispatch('edit-pesanan', id: $id);
    }

    public function delete($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->details()->delete();
        $pesanan->delete();
    }

    public function exportExcel()
    {
        return Excel::download(new PesananExport($this->search), 'pesanan.xlsx');
    }

    public function render()
    {
        return view('livewire.pesanan-table', [
            'pesananList' => Pesanan::with('details.obat')
                ->where(function ($query) {
                    $query->where('no_sp', 'like', '%' . $this->search . '%')
                        ->orWhere('tanggal', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(5)
        ]);
    }
}
