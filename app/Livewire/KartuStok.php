<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Obat;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\KartuStokExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\KartuStok as KartuStokModel;

class KartuStok extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind'; // pakai Tailwind pagination

    public $obat_id;
    public $start_date;
    public $end_date;

    public $searchObat = '';
    public $obatResults = [];
    public $highlightIndex = 0;

    public function mount()
    {
        $this->start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->end_date   = Carbon::today()->format('Y-m-d');
    }

    // Reset halaman saat filter berubah
    public function updatingObatId()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    // Search obat untuk autocomplete
    public function updatedSearchObat()
    {
        if (strlen($this->searchObat) > 1) {
            $this->obatResults = Obat::where('nama_obat', 'like', '%' . $this->searchObat . '%')
                ->limit(10)
                ->get()
                ->toArray();
            $this->highlightIndex = 0;
        } else {
            $this->obatResults = [];
            $this->highlightIndex = 0;
        }
    }

    public function incrementHighlight()
    {
        if (count($this->obatResults) === 0) return;
        $this->highlightIndex++;
        if ($this->highlightIndex >= count($this->obatResults)) {
            $this->highlightIndex = 0;
        }
    }

    public function decrementHighlight()
    {
        if (count($this->obatResults) === 0) return;
        $this->highlightIndex--;
        if ($this->highlightIndex < 0) {
            $this->highlightIndex = count($this->obatResults) - 1;
        }
    }

    public function selectHighlighted()
    {
        if (isset($this->obatResults[$this->highlightIndex])) {
            $this->selectObat($this->obatResults[$this->highlightIndex]['id']);
        }
    }

    public function selectObat($id)
    {
        $obat = Obat::find($id);
        if ($obat) {
            $this->obat_id = $obat->id;
            $this->searchObat = $obat->nama_obat;
            $this->obatResults = [];
            $this->highlightIndex = 0;
        }
    }

    // Export Excel
    public function exportExcel()
    {
        return Excel::download(
            new KartuStokExport($this->start_date, $this->end_date, $this->obat_id),
            'kartu_stok.xlsx'
        );
    }

    // public function render()
    // {
    //     $obatList = Obat::orderBy('nama_obat')->get();

    //     $query = KartuStokModel::with([
    //         'obat',
    //         'penerimaan',
    //         'mutasi',
    //         'penerimaanDetail.satuan',
    //         'penerimaanDetail.penerimaan.kreditur',
    //         'pabrik',
    //         'sediaan',
    //         'satuan',
    //         'obat.kategori',
    //     ]);

    //     if ($this->obat_id) {
    //         $query->where('obat_id', $this->obat_id);
    //     }

    //     if ($this->start_date && $this->end_date) {
    //         $query->whereBetween('tanggal', [$this->start_date, $this->end_date]);
    //     }

    //     // Pagination 15 baris per halaman
    //     $riwayat = $query->orderBy('tanggal')
    //         ->orderBy('id')
    //         ->paginate(10);

    //     // Hitung stok akhir berjalan per obat+batch+ed
    //     $saldoPerObat = [];
    //     foreach ($riwayat as $row) {
    //         $key = $row->obat_id . '-' . $row->batch . '-' . $row->ed;
    //         if (!isset($saldoPerObat[$key])) {
    //             $saldoPerObat[$key] = 0;
    //         }
    //         $saldoPerObat[$key] += ($row->jenis === 'masuk' ? $row->qty : -$row->qty);
    //         $row->stok_akhir = $saldoPerObat[$key];
    //     }

    //     return view('livewire.kartu-stok', [
    //         'obatList' => $obatList,
    //         'riwayat'  => $riwayat,
    //     ]);
    // }

    public function render()
    {
        $obatList = Obat::orderBy('nama_obat')->get();

        // Query utama ambil semua kolom kartu stok dengan relasi
        $query = KartuStokModel::with([
            'obat',
            'penerimaan',
            'mutasi',
            'penerimaanDetail.satuan',
            'penerimaanDetail.penerimaan.kreditur',
            'pabrik',
            'sediaan',
            'satuan',
            'obat.kategori',
        ]);

        // Filter berdasarkan obat
        if ($this->obat_id) {
            $query->where('obat_id', $this->obat_id);
        }

        // Filter tanggal
        if ($this->start_date && $this->end_date) {
            $query->whereBetween('tanggal', [$this->start_date, $this->end_date]);
        }

        // Urutkan berdasarkan tanggal dan id
        $riwayat = $query->orderBy('tanggal')
            ->orderBy('id')
            ->paginate(15);

        return view('livewire.kartu-stok', [
            'obatList' => $obatList,
            'riwayat'  => $riwayat,
        ]);
    }
}
