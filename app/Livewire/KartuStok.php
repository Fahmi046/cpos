<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Obat;
use Livewire\Component;
use App\Models\KartuStok as KartuStokModel;

class KartuStok extends Component
{
    public $obat_id;
    public $start_date;
    public $end_date;

    public function mount()
    {
        // Default tanggal awal & akhir hari ini
        $today = Carbon::today()->format('Y-m-d');
        $this->start_date = $today;
        $this->end_date   = $today;
    }


    public function render()
    {
        $obatList = Obat::orderBy('nama_obat')->get();

        $query = KartuStokModel::with([
            'obat',
            'penerimaan',
            'mutasi',
            'penerimaanDetail.satuan',
            'penerimaanDetail.penerimaan.kreditur',
            'penerimaanDetail.pabrik',
            'obat.kategori',
        ]);

        if ($this->obat_id) {
            $query->where('obat_id', $this->obat_id);
        }

        if ($this->start_date && $this->end_date) {
            $query->whereBetween('tanggal', [$this->start_date, $this->end_date]);
        }

        $riwayat = $query->orderBy('tanggal')->orderBy('id')->get();

        // Hitung stok akhir berjalan
        $saldo = 0;
        foreach ($riwayat as $row) {
            $saldo += ($row->jenis === 'masuk' ? $row->qty : -$row->qty);
            $row->stok_akhir = $saldo;
        }

        return view('livewire.kartu-stok', [
            'obatList' => $obatList,
            'riwayat'  => $riwayat,
        ]);
    }
    public $searchObat = '';
    public $obatResults = [];
    public $highlightIndex = 0;
    public function updatedSearchObat()
    {
        if (strlen($this->searchObat) > 1) {
            $this->obatResults = Obat::where('nama_obat', 'like', '%' . $this->searchObat . '%')
                ->limit(10)
                ->get()
                ->toArray(); // <- pastikan array supaya index gampang
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
}
