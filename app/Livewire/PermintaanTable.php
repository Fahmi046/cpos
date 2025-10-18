<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Permintaan;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\PermintaanExport;
use App\Exports\PermintaanExportSummary;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermintaanExportDetailed;
use Illuminate\Support\Facades\Auth;


class PermintaanTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshTable' => 'loadData'];

    public $search = '';
    public $selectedId;
    public $no_permintaan, $tanggal, $outlet_id, $keterangan = '';
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
        $permintaan = Permintaan::with('details.obat')->findOrFail($id);

        $this->no_permintaan  = $permintaan->no_permintaan;
        $this->tanggal    = $permintaan->tanggal;
        $this->outlet_id  = $permintaan->outlet_id;
        $this->keterangan = $permintaan->keterangan;

        $this->details = $permintaan->details->map(function ($detail) {
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
        $permintaan = Permintaan::findOrFail($id);
        $permintaan->details()->delete();
        $permintaan->delete();
        $this->loadData();
        session()->flash('message', 'permintaan berhasil dihapus.');

        $this->dispatch('refreshKodepermintaan');
        $this->dispatch('focus-tanggal');
    }

    public function exportExcelDetailed()
    {
        $tanggal = Carbon::now()->format('Y-m-d');
        $fileName = $this->start_date && $this->end_date
            ? "permintaan_{$this->start_date}_sd_{$this->end_date}.xlsx"
            : "permintaan_{$tanggal}.xlsx";

        return Excel::download(new PermintaanExportDetailed($this->search, $this->start_date, $this->end_date), $fileName);
    }

    public function exportExcelSummary()
    {
        $tanggal = Carbon::now()->format('Y-m-d');
        $fileName = $this->start_date && $this->end_date
            ? "permintaan_{$this->start_date}_sd_{$this->end_date}.xlsx"
            : "permintaan_{$tanggal}.xlsx";

        return Excel::download(new PermintaanExportSummary($this->search, $this->start_date, $this->end_date), $fileName);
    }

    public function render()
    {
        $query = Permintaan::with(['details', 'outlet'])
            ->where(function ($query) {
                $query->where('no_permintaan', 'like', '%' . $this->search . '%')
                    ->orWhere('tanggal', 'like', '%' . $this->search . '%')
                    ->orWhereHas('details.obat', function ($q) {
                        $q->where('nama_obat', 'like', '%' . $this->search . '%');
                    });
            });

        // 🔒 Filter otomatis jika user adalah outlet
        if (Auth::check() && Auth::user()->role === 'outlet') {
            $query->where('outlet_id', Auth::user()->outlet_id);
        }

        // Urutkan: pending/sebagian dulu, lalu tanggal terbaru
        $permintaanList = $query
            ->orderByRaw("CASE WHEN status = 'sebagian' OR status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('tanggal', 'desc')
            ->paginate(5);

        return view('livewire.permintaan-table', [
            'permintaanList' => $permintaanList
        ]);
    }

    public function mount()
    {
        $this->loadData();
    }

    public $permintaans;

    public function loadData()
    {
        $this->permintaans = Permintaan::orderBy('no_permintaan')->get();
        $this->search = '';
        $this->start_date = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->end_date   = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function print($id)
    {
        return redirect()->route('permintaan.print', $id);
    }
}
