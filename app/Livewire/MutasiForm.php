<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Obat;
use App\Models\Mutasi;
use App\Models\Outlet;
use Livewire\Component;
use App\Models\KartuStok;
use App\Models\MutasiDetail;

class MutasiForm extends Component
{
    public $mutasi_id;
    public $no_mutasi;
    public $tanggal;
    public $outlet_id;
    public $keterangan;

    public $details = []; // array detail mutasi
    public $obat_id;
    public $qty = 1;
    public $batch;
    public $ed;

    public $outlets = [];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');;
        $this->details = [$this->emptyDetailRow()];
        $this->outlets = \App\Models\Outlet::all();
        $this->no_mutasi = $this->generateNoMT();
    }

    protected function emptyDetailRow(): array
    {
        return [
            'obat_id'    => null,
            'pabrik_id'  => null,
            'satuan_id'  => null,
            'sediaan_id' => null,
            'qty'        => 0,
            'ed' => date('Y-m-d'), // 🔹 default tanggal sekarang
            'batch'      => '',
            'disc1'      => 0,
            'disc2'      => 0,
            'disc3'      => 0,
            'utuh'       => false, // utuhan atau tidak
        ];
    }
    public function save()
    {
        // jika ada obat_id belum ditambahkan ke detail, tambahkan otomatis
        if ($this->obat_id) {
            $this->addDetail();
        }

        $this->validate([
            'no_mutasi' => 'required|unique:mutasi,no_mutasi,' . ($this->mutasi_id ?? ''),
            'tanggal' => 'required|date',
            'outlet_id' => 'required|exists:outlets,id',
            'detail.*.obat_id' => 'required|exists:obat,id',
            'detail.*.qty' => 'required|numeric|min:1',
        ]);

        if ($this->mutasi_id) {
            $mutasi = \App\Models\Mutasi::findOrFail($this->mutasi_id);
            $mutasi->update([
                'no_mutasi' => $this->no_mutasi,
                'tanggal' => $this->tanggal,
                'outlet_id' => $this->outlet_id,
                'keterangan' => $this->keterangan,
            ]);
            $mutasi->details()->delete();
        } else {
            $mutasi = \App\Models\Mutasi::create([
                'no_mutasi' => $this->no_mutasi,
                'tanggal' => $this->tanggal,
                'outlet_id' => $this->outlet_id,
                'keterangan' => $this->keterangan,
            ]);
        }

        foreach ($this->detail as $d) {
            $obat = \App\Models\Obat::find($d['obat_id']);
            if (!$obat) continue;

            $mutasi->details()->create([
                'obat_id' => $obat->id,
                'pabrik_id' => $obat->pabrik_id ?? 1,
                'satuan_id' => $obat->satuan_id ?? 1,
                'sediaan_id' => $obat->sediaan_id ?? 1,
                'qty' => $d['qty'] ?? 1,
                'batch' => $d['batch'] ?? null,
                'ed' => $d['ed'] ?? null,
                'jumlah' => ($d['qty'] ?? 1) * ($d['harga'] ?? 0),
                'utuhan' => $d['utuhan'] ?? 0,
            ]);
        }

        session()->flash('message', $this->mutasi_id ? 'Mutasi berhasil diperbarui!' : 'Mutasi berhasil disimpan!');

        // Reset form
        $this->resetForm();
        $this->dispatch('refreshTable');
        $this->dispatch('focus-tanggal');
    }

    private function resetForm()
    {

        $this->mutasi_id = null;
        $this->no_mutasi = 'MTS-' . now()->format('Ymd-His');
        $this->tanggal = date('Y-m-d');
        $this->outlet_id = null;
        $this->keterangan = '';
        $this->details = [];
        $this->obat_id = null;
        $this->qty = 1;
        $this->batch = '';
        $this->ed = '';
    }


    public function render()
    {
        return view('livewire.mutasi-form');
    }

    protected $branchCode = 'GUDANG';

    public function generateNoMT()
    {
        $tanggal = Carbon::parse($this->tanggal);
        $year = $tanggal->format('Y');
        $month = $this->monthToRoman((int)$tanggal->format('n'));

        // Hitung urutan MT tahun ini
        $count = Mutasi::whereYear('tanggal', $year)->count() + 1;
        $seq = str_pad($count, 4, '0', STR_PAD_LEFT);

        // Format MT
        return "MT-{$seq}/{$month}/{$year}";
    }

    public function updatedTanggal()
    {
        $this->no_mutasi = $this->generateNoMT();
    }

    private function monthToRoman($month)
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romans[(int) $month] ?? $month;
    }


    public $searchoutlet = '';
    public $outletResults = [];
    public $highlightIndex = 0;
    public function updatedSearchoutlet()
    {
        if (strlen($this->searchoutlet) > 1) {
            $this->outletResults = outlet::where('nama_outlet', 'like', '%' . $this->searchoutlet . '%')
                ->limit(10)
                ->get()
                ->toArray(); // <- pastikan array supaya index gampang
            $this->highlightIndex = 0;
        } else {
            $this->outletResults = [];
            $this->highlightIndex = 0;
        }
    }

    public function incrementHighlight()
    {
        if (count($this->outletResults) === 0) return;

        $this->highlightIndex++;
        if ($this->highlightIndex >= count($this->outletResults)) {
            $this->highlightIndex = 0;
        }
    }

    public function decrementHighlight()
    {
        if (count($this->outletResults) === 0) return;

        $this->highlightIndex--;
        if ($this->highlightIndex < 0) {
            $this->highlightIndex = count($this->outletResults) - 1;
        }
    }

    public function selectHighlighted()
    {
        if (isset($this->outletResults[$this->highlightIndex])) {
            $this->selectoutlet($this->outletResults[$this->highlightIndex]['id']);
        }
    }

    public function selectoutlet($id)
    {
        $outlet = Outlet::find($id);
        if ($outlet) {
            $this->outlet_id = $outlet->id;
            $this->searchoutlet = $outlet->nama_outlet;
            $this->outletResults = [];
            $this->highlightIndex = 0;
        }
    }


    public $obatSearch = [];        // array untuk tiap baris detail
    public $obatResults = [];       // hasil pencarian obat per baris
    public $highlightObatIndex = []; // highlight keyboard per baris

    public function updatedObatSearch($value, $index)
    {
        if ($value) {
            $this->obatResults[$index] = KartuStok::with('obat')
                ->select('id', 'obat_id', 'batch', 'ed')
                ->whereHas('obat', function ($q) use ($value) {
                    $q->where('nama_obat', 'like', "%{$value}%");
                })
                ->groupBy('id', 'obat_id', 'batch', 'ed')
                ->orderBy('ed', 'asc')
                ->limit(5)
                ->get();
        } else {
            $this->obatResults[$index] = [];
        }

        $this->highlightObatIndex[$index] = 0;
    }


    public function selectObat($index, $obat_id)
    {
        $obat = Obat::with(['satuan', 'sediaan', 'pabrik'])->find($obat_id);
        if (!$obat) return;

        // ambil data terakhir dari kartu stok
        $kartuStok = KartuStok::where('obat_id', $obat_id)
            ->latest('tanggal') // atau kolom created_at
            ->first();

        $this->details[$index]['obat_id']   = $obat->id;
        $this->details[$index]['nama_obat'] = $obat->nama_obat;
        $this->details[$index]['isi_obat']  = $obat->isi_obat ?? 1;

        // Harga → ambil dari kartu_stok kalau ada, fallback ke harga_beli di obat
        $harga = $kartuStok->harga_beli ?? $obat->harga_beli ?? 0;
        $this->details[$index]['harga']  = $harga;
        $this->details[$index]['qty']    = 1;
        $this->details[$index]['jumlah'] = $harga;

        // Relasi
        $this->details[$index]['pabrik_id'] = $obat->pabrik_id;
        $this->details[$index]['pabrik']    = $obat->pabrik->nama_pabrik ?? '';

        $this->details[$index]['satuan_id'] = $obat->satuan_id;
        $this->details[$index]['satuan']    = $obat->satuan->nama_satuan ?? '';

        $this->details[$index]['sediaan_id'] = $obat->sediaan_id;
        $this->details[$index]['sediaan']    = $obat->sediaan->nama_sediaan ?? '';

        // batch & ED kalau ada di kartu stok
        $this->details[$index]['batch'] = $kartuStok->batch ?? '';
        $this->details[$index]['ed']    = $kartuStok->ed ?? null;

        // default utuh true kalau isi_obat > 1
        $this->details[$index]['utuh'] = ($obat->isi_obat ?? 1) > 1;

        // reset search box di row itu
        $this->obatSearch[$index] = $obat->nama_obat;
        $this->obatResults[$index] = [];
        $this->highlightObatIndex[$index] = 0;
    }


    public function toggleUtuhSatuan($index)
    {
        if (!isset($this->details[$index])) return;

        $detail = $this->details[$index];
        $obatId = $detail['obat_id'] ?? null;

        if (!$obatId) return;

        $obat = Obat::with(['satuan', 'sediaan'])->find($obatId);
        if (!$obat) return;

        if (!empty($detail['utuh']) && $detail['utuh']) {
            $this->details[$index]['qty']       = $obat->isi_obat ?? 1;
            $this->details[$index]['satuan_id'] = $obat->satuan_id;
            $this->details[$index]['satuan']    = $obat->satuan->nama_satuan ?? 'SET';
            $this->details[$index]['isi_obat']  = $obat->isi_obat ?? 1;
            $this->details[$index]['harga']     = $obat->harga_jual ?? 0;
        } else {
            $this->details[$index]['qty']       = 1;
            $this->details[$index]['satuan_id'] = $obat->sediaan_id;
            $this->details[$index]['satuan']    = $obat->sediaan->nama_sediaan ?? 'PCS';
            $this->details[$index]['isi_obat']  = 1;
            $this->details[$index]['harga']     = $obat->harga_jual_eceran ?? ($obat->harga_jual / max($obat->isi_obat, 1));
        }

        // hitung ulang jumlah
        $this->details[$index]['jumlah'] = ($this->details[$index]['qty'] ?? 1) * ($this->details[$index]['harga'] ?? 0);

        // 🔹 Penting: hitung ringkasan DPP, PPN, TOTAL
        $this->hitungRingkasan();
    }

    public function highlightNextObat($index)
    {
        if (isset($this->obatResults[$index][$this->highlightObatIndex[$index] + 1])) {
            $this->highlightObatIndex[$index]++;
        }
    }

    public function highlightPrevObat($index)
    {
        if ($this->highlightObatIndex[$index] > 0) {
            $this->highlightObatIndex[$index]--;
        }
    }

    public function selectHighlightedObat($index)
    {
        if (isset($this->obatResults[$index][$this->highlightObatIndex[$index]])) {
            $this->selectObat($index, $this->obatResults[$index][$this->highlightObatIndex[$index]]->id);
        }
    }
}
