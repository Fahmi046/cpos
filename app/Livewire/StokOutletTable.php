<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Outlet;
use App\Models\Obat;
use App\Models\StokOutlet;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StokOutletExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StokOutletTable extends Component
{
    use WithPagination;

    public $searchOutlet = '';
    public $outletResults = [];
    public $searchObat = '';
    public $obatResults = [];

    public $highlightIndex = 0;
    public $highlightObatIndex = 0;

    public $selectedOutletId = null;
    public $selectedObatId = null;

    public $start_date;
    public $end_date;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->end_date   = Carbon::now()->endOfMonth()->format('Y-m-d');

        // 🔹 Jika user bukan admin, otomatis gunakan outlet miliknya
        if (Auth::check() && Auth::user()->role !== 'admin') {
            $this->selectedOutletId = Auth::user()->outlet_id;
        }
    }

    // ========== OUTLET SEARCH (khusus admin) ==========
    public function updatedSearchOutlet()
    {
        if (Auth::user()->role !== 'admin') return; // 🔒 non-admin tidak bisa ganti outlet

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
        if ($this->highlightIndex < count($this->outletResults) - 1) $this->highlightIndex++;
    }

    public function decrementHighlight()
    {
        if ($this->highlightIndex > 0) $this->highlightIndex--;
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
        if (Auth::user()->role !== 'admin') return; // 🔒 hanya admin
        $outlet = Outlet::find($id);
        if ($outlet) {
            $this->selectedOutletId = $outlet->id;
            $this->searchOutlet     = $outlet->nama_outlet;
            $this->outletResults    = [];
        }
    }

    // ========== OBAT SEARCH (filter by outlet login) ==========
    public function updatedSearchObat()
    {
        if (strlen($this->searchObat) > 1) {
            $outletId = $this->selectedOutletId ?? Auth::user()->outlet_id;

            $this->obatResults = Obat::query()
                ->join('stok_outlet', 'obat.id', '=', 'stok_outlet.obat_id')
                ->where('stok_outlet.outlet_id', $outletId)
                ->where(function ($query) {
                    $query->where('obat.nama_obat', 'like', '%' . $this->searchObat . '%');
                })
                ->select('obat.id', 'obat.nama_obat')
                ->distinct()
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->obatResults = [];
        }
    }

    public function incrementObatHighlight()
    {
        if ($this->highlightObatIndex < count($this->obatResults) - 1) $this->highlightObatIndex++;
    }

    public function decrementObatHighlight()
    {
        if ($this->highlightObatIndex > 0) $this->highlightObatIndex--;
    }

    public function selectObatHighlighted()
    {
        if (!empty($this->obatResults)) {
            $obat = $this->obatResults[$this->highlightObatIndex];
            $this->selectObat($obat['id']);
        }
    }

    public function selectObat($id)
    {
        $obat = Obat::find($id);
        if ($obat) {
            $this->selectedObatId = $obat->id;
            $this->searchObat     = $obat->nama_obat;
            $this->obatResults    = [];
        }
    }

    // ========== EXPORT ==========
    public function exportExcel()
    {
        $outletId = $this->selectedOutletId ?? Auth::user()->outlet_id;

        if (!$outletId) {
            session()->flash('error', 'Outlet tidak ditemukan untuk pengguna ini!');
            return;
        }

        $filename = 'stok_outlet_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new StokOutletExport(
            $outletId,
            $this->start_date,
            $this->end_date
        ), $filename);
    }

    // ========== RENDER ==========
    public function render()
    {
        $outletId = $this->selectedOutletId ?? Auth::user()->outlet_id;

        $subQuery = StokOutlet::selectRaw('MAX(id) as last_id')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->when($this->selectedObatId, fn($q) => $q->where('obat_id', $this->selectedObatId))
            ->when($this->start_date, fn($q) => $q->whereDate('tanggal', '>=', $this->start_date))
            ->when($this->end_date, fn($q) => $q->whereDate('tanggal', '<=', $this->end_date))
            ->groupBy('obat_id');

        $query = StokOutlet::with(['obat', 'outlet'])
            ->joinSub($subQuery, 'latest', function ($join) {
                $join->on('stok_outlet.id', '=', 'latest.last_id');
            })
            ->when($outletId, fn($q) => $q->where('stok_outlet.outlet_id', $outletId))
            ->orderBy('tanggal', 'desc');

        return view('livewire.stok-outlet', [
            'stokOutlet' => $query->paginate(10),
        ]);
    }
}
