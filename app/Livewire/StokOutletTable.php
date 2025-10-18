<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Outlet;
use App\Models\StokOutlet;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StokOutletExport;
use Carbon\Carbon;

class StokOutletTable extends Component
{
    use WithPagination;

    public $searchOutlet = '';
    public $outletResults = [];
    public $highlightIndex = 0;

    public $selectedOutletId = null;
    public $start_date;
    public $end_date;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->end_date   = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedSearchOutlet()
    {
        if (strlen($this->searchOutlet) > 1) {
            $this->outletResults = Outlet::where('nama_outlet', 'like', '%' . $this->searchOutlet . '%')
                ->select('id', 'nama_outlet')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->outletResults = [];
        }
    }

    public function incrementHighlight()
    {
        if ($this->highlightIndex < count($this->outletResults) - 1) {
            $this->highlightIndex++;
        }
    }

    public function decrementHighlight()
    {
        if ($this->highlightIndex > 0) {
            $this->highlightIndex--;
        }
    }

    public function selectHighlighted()
    {
        if (!empty($this->outletResults)) {
            $outlet = $this->outletResults[$this->highlightIndex];
            $this->selectOutlet($outlet['id']);
        }
    }

    public function selectOutlet($id)
    {
        $outlet = Outlet::find($id);
        if ($outlet) {
            $this->selectedOutletId = $outlet->id;
            $this->searchOutlet     = $outlet->nama_outlet;
            $this->outletResults    = [];
        }
    }

    public function exportExcel()
    {
        if (!$this->selectedOutletId) {
            session()->flash('error', 'Pilih outlet terlebih dahulu!');
            return;
        }

        $filename = 'stok_outlet_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new StokOutletExport(
            $this->selectedOutletId,
            $this->start_date,
            $this->end_date
        ), $filename);
    }

    public function render()
    {
        // Ambil stok terakhir per obat, per outlet (latest record)
        $subQuery = StokOutlet::selectRaw('MAX(id) as last_id')
            ->when($this->selectedOutletId, fn($q) => $q->where('outlet_id', $this->selectedOutletId))
            ->when($this->start_date, fn($q) => $q->whereDate('tanggal', '>=', $this->start_date))
            ->when($this->end_date, fn($q) => $q->whereDate('tanggal', '<=', $this->end_date))
            ->groupBy('obat_id');

        $query = StokOutlet::with(['obat', 'outlet'])
            ->joinSub($subQuery, 'latest', function ($join) {
                $join->on('stok_outlet.id', '=', 'latest.last_id');
            })
            ->orderBy('tanggal', 'desc');

        return view('livewire.stok-outlet', [
            'stokOutlet' => $query->paginate(10),
        ]);
    }
}
