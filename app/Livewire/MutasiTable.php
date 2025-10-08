<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Mutasi;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\MutasiExport;
use App\Exports\MutasiExportSummary;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MutasiExportDetailed;

class MutasiTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshTable' => 'loadData'];

    public $search = '';
    public $selectedId;
    public $no_mutasi, $tanggal, $outlet_id, $keterangan = '';
    public $details = [];
    public $start_date;
    public $end_date;


    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $this->selectedId = $id;
        $mutasi = Mutasi::with('details.obat')->findOrFail($id);

        $this->no_mutasi  = $mutasi->no_mutasi;
        $this->tanggal    = $mutasi->tanggal;
        $this->outlet_id  = $mutasi->outlet_id;
        $this->keterangan = $mutasi->keterangan;

        $this->details = $mutasi->details->map(function ($detail) {
            return [
                'obat_id'    => $detail->obat_id,
                'nama_obat'  => $detail->obat->nama_obat ?? '',
                'qty'        => $detail->qty,
                'harga'      => $detail->harga,
                'jumlah'     => $detail->jumlah,
                'ed'         => $detail->ed,
                'batch'      => $detail->batch,
                'satuan_id'  => $detail->satuan_id,
                'sediaan_id' => $detail->sediaan_id,
                'pabrik_id'  => $detail->pabrik_id,
                'utuh'       => $detail->utuh,
            ];
        })->toArray();

        $this->dispatch('focus-tanggal');
    }

    public function delete($id)
    {
        $mutasi = Mutasi::findOrFail($id);
        $mutasi->details()->delete();
        $mutasi->delete();
        $this->loadData();
        session()->flash('message', 'Mutasi berhasil dihapus.');

        $this->dispatch('refreshKodeMutasi');
        $this->dispatch('focus-tanggal');
    }

    public function exportExcelDetailed()
    {
        $tanggal = Carbon::now()->format('Y-m-d');
        $fileName = $this->start_date && $this->end_date
            ? "mutasi_{$this->start_date}_sd_{$this->end_date}.xlsx"
            : "mutasi_{$tanggal}.xlsx";

        return Excel::download(new MutasiExportDetailed($this->search, $this->start_date, $this->end_date), $fileName);
    }

    public function exportExcelSummary()
    {
        $tanggal = Carbon::now()->format('Y-m-d');
        $fileName = $this->start_date && $this->end_date
            ? "mutasi_{$this->start_date}_sd_{$this->end_date}.xlsx"
            : "mutasi_{$tanggal}.xlsx";

        return Excel::download(new MutasiExportSummary($this->search, $this->start_date, $this->end_date), $fileName);
    }


    public function render()
    {
        $mutasiList = Mutasi::with([
            'details.obat',
            'details.penerimaanDetail',
            'outlet'
        ])
            ->where(function ($query) {
                $query->where('no_mutasi', 'like', '%' . $this->search . '%')
                    ->orWhere('tanggal', 'like', '%' . $this->search . '%')
                    ->orWhereHas('details.obat', function ($q) {
                        $q->where('nama_obat', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(5);

        return view('livewire.mutasi-table', [
            'mutasiList' => $mutasiList
        ]);
    }

    public function mount()
    {
        $this->loadData();
    }

    public $mutasis;

    public function loadData()
    {
        $this->mutasis = Mutasi::orderBy('no_mutasi')->get();
        $this->search = '';
        $this->start_date = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->end_date   = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function print($id)
    {
        return redirect()->route('mutasi.print', $id);
    }
}
