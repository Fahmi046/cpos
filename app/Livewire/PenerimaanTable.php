<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Penerimaan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenerimaanExport; // nanti kita buat Export class

class PenerimaanTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshTable' => 'loadData'];

    public $search = '';
    public $start_date;
    public $end_date;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $penerimaanList = Penerimaan::with(['pesanan', 'kreditur'])
            ->when($this->search, function ($query) {
                $query->where('no_penerimaan', 'like', '%' . $this->search . '%')
                    ->orWhere('no_faktur', 'like', '%' . $this->search . '%') // tambah ini
                    ->orWhere('tanggal', 'like', '%' . $this->search . '%')
                    ->orWhereHas('pesanan', function ($q) {
                        $q->where('no_sp', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('kreditur', function ($q) {
                        $q->where('nama', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->start_date, fn($q) => $q->whereDate('tanggal', '>=', $this->start_date))
            ->when($this->end_date, fn($q) => $q->whereDate('tanggal', '<=', $this->end_date))
            ->latest('id')
            ->paginate(5);

        return view('livewire.penerimaan-table', [
            'penerimaanList' => $penerimaanList
        ]);
    }


    public function mount()
    {
        // Tanggal awal = awal bulan ini
        $this->start_date = now()->startOfMonth()->format('Y-m-d');

        // Tanggal akhir = hari ini
        $this->end_date = now()->format('Y-m-d');

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

    public function exportExcel()
    {
        $start = $this->start_date;
        $end = $this->end_date;

        return Excel::download(new PenerimaanExport($start, $end), 'penerimaan.xlsx');
    }
}
