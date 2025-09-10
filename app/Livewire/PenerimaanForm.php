<?php

namespace App\Livewire;

use App\Models\Penerimaan;
use App\Models\Pesanan;
use App\Models\{BentukSediaan, Obat, Pabrik, Satuan, SatuanObat, Sediaan};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PenerimaanForm extends Component
{
    public $penerimaan_id;

    // Header Penerimaan
    public $pesanan_id, $tanggal, $no_penerimaan, $jenis_bayar, $kreditur_id, $kreditur_nama;
    public $no_faktur, $tenor, $jatuh_tempo, $jenis_ppn;

    // DETAIL PENERIMAAN (<<— inilah yang dimaksud $details)
    public $details = [];   // <- WAJIB ada supaya tidak "Undefined variable $details"

    public function mount(?int $penerimaan_id = null)
    {
        $this->penerimaan_id = $penerimaan_id;

        // minimal 1 baris kosong detail
        $this->details = [$this->emptyDetailRow()];

        $this->obatSearch = [''];
        $this->obatResults = [[]];
        $this->highlightObatIndex = [0];

        // 🔹 DEFAULT HEADER
        $this->tanggal       = Carbon::now()->format('Y-m-d'); // hari ini
        $this->jenis_bayar   = 'KREDIT';
        $this->jenis_ppn     = 'INCLUDE';

        // 🔹 NO PENERIMAAN OTOMATIS
        $this->no_penerimaan = $this->generateNoPenerimaan($this->tanggal);

        // 🔹 Set tenor & jatuh tempo awal
        $this->setTenorAndJatuhTempo();
    }

    protected function emptyDetailRow(): array
    {
        return [
            'obat_id'    => null,
            'pabrik_id'  => null,
            'satuan_id'  => null,
            'sediaan_id' => null,
            'qty'        => 0,
            'ed'         => null,
            'batch'      => '',
            'disc1'      => 0,
            'disc2'      => 0,
            'disc3'      => 0,
            'utuh'       => false, // utuhan atau tidak
        ];
    }

    public function addDetail()
    {
        $this->details[] = $this->emptyDetailRow();
        $this->obatSearch[] = '';
        $this->obatResults[] = [];
        $this->highlightObatIndex[] = 0;
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details); // reindex
    }

    public function loadPesanan()
    {
        if (!$this->pesanan_id) {
            $this->details = [$this->emptyDetailRow()];
            $this->obatSearch = [''];
            $this->obatResults = [[]];
            $this->highlightObatIndex = [0];
            return;
        }

        $pesanan = Pesanan::with('details.obat')->find($this->pesanan_id);

        if (!$pesanan) {
            $this->details = [$this->emptyDetailRow()];
            $this->obatSearch = [''];
            $this->obatResults = [[]];
            $this->highlightObatIndex = [0];
            return;
        }

        $this->details = [];
        $this->obatSearch = [];
        $this->obatResults = [];
        $this->highlightObatIndex = [];

        foreach ($pesanan->details as $d) {
            $this->details[] = [
                'obat_id'    => $d->obat_id,
                'pabrik_id'  => $d->pabrik_id ?? null,
                'satuan_id'  => $d->satuan_id,
                'sediaan_id' => $d->sediaan_id ?? null,
                'qty'        => $d->qty,
                'ed'         => null,
                'batch'      => '',
                'disc1'      => 0,
                'disc2'      => 0,
                'disc3'      => 0,
                'utuh'       => false,
            ];

            // 🔹 penting: isi nama obat agar tampil di input auto-complete
            $this->obatSearch[] = $d->obat->nama_obat ?? '';
            $this->obatResults[] = [];
            $this->highlightObatIndex[] = 0;
        }

        // jika kosong, tambah satu baris
        if (empty($this->details)) {
            $this->details = [$this->emptyDetailRow()];
            $this->obatSearch = [''];
            $this->obatResults = [[]];
            $this->highlightObatIndex = [0];
        }
    }



    public function save()
    {
        $this->validate([
            'pesanan_id' => 'required|exists:pesanan,id',
            'tanggal'    => 'required|date',
            'jenis_bayar' => 'required|in:CASH,KREDIT',

            'details'                 => 'required|array|min:1',
            'details.*.obat_id'       => 'required|exists:obat,id',
            'details.*.pabrik_id'     => 'nullable|exists:pabrik,id',
            'details.*.satuan_id'     => 'required|exists:satuan,id',
            'details.*.sediaan_id'    => 'nullable|exists:sediaan,id',
            'details.*.qty'           => 'required|numeric|min:1',
            'details.*.ed'            => 'nullable|date',
            'details.*.batch'         => 'nullable|string|max:50',
            'details.*.disc1'         => 'nullable|numeric|min:0',
            'details.*.disc2'         => 'nullable|numeric|min:0',
            'details.*.disc3'         => 'nullable|numeric|min:0',
            'details.*.utuh'          => 'boolean',
        ]);

        DB::transaction(function () {
            $penerimaan = Penerimaan::updateOrCreate(
                ['id' => $this->penerimaan_id],
                [
                    'pesanan_id'    => $this->pesanan_id,
                    'tanggal'       => $this->tanggal,
                    'no_penerimaan' => $this->no_penerimaan,
                    'jenis_bayar'   => $this->jenis_bayar,
                    'kreditur_id'   => $this->kreditur_id,
                    'no_faktur'     => $this->no_faktur,
                    'tenor'         => $this->tenor,
                    'jatuh_tempo'   => $this->jatuh_tempo,
                    'jenis_ppn'     => $this->jenis_ppn,
                ]
            );

            // sederhana: hapus & buat ulang detail
            $penerimaan->details()->delete();

            foreach ($this->details as $row) {
                $penerimaan->details()->create([
                    'obat_id'    => $row['obat_id'],
                    'pabrik_id'  => $row['pabrik_id'],
                    'satuan_id'  => $row['satuan_id'],
                    'sediaan_id' => $row['sediaan_id'],
                    'qty'        => $row['qty'],
                    'ed'         => $row['ed'],
                    'batch'      => $row['batch'],
                    'disc1'      => $row['disc1'] ?? 0,
                    'disc2'      => $row['disc2'] ?? 0,
                    'disc3'      => $row['disc3'] ?? 0,
                    'utuh'       => !empty($row['utuh']),
                ]);
            }
        });

        session()->flash('success', 'Penerimaan disimpan.');
        return redirect()->route('penerimaan.index');
    }

    public function render()
    {
        return view('livewire.penerimaan-form', [
            'pesananList' => Pesanan::select('id', 'no_sp', 'tanggal')->latest()->get(),
            'obatList'    => Obat::orderBy('nama_obat')->get(),
            'pabrikList'  => Pabrik::orderBy('nama_pabrik')->get(),
            'satuanList'  => SatuanObat::orderBy('nama_satuan')->get(),
            'sediaanList' => BentukSediaan::orderBy('nama_sediaan')->get(),
        ]);
    }
    public $search = '';
    public $pesananList = [];
    public $highlightIndex = 0; // untuk navigasi keyboard

    public function updatedSearch()
    {
        if ($this->search) {
            $this->pesananList = Pesanan::where('no_sp', 'like', "%{$this->search}%")
                ->orWhere('tanggal', 'like', "%{$this->search}%")
                ->orderBy('tanggal', 'desc')
                ->limit(5)
                ->get();
        } else {
            $this->pesananList = [];
        }
        $this->highlightIndex = 0; // reset highlight saat search berubah
    }
    public function selectPesanan($id)
    {
        $pesanan = Pesanan::with(['details.obat.satuan', 'details.obat.sediaan', 'details.obat.pabrik'])
            ->find($id);

        if (!$pesanan) return;

        $this->pesanan_id = $pesanan->id;
        $this->search = $pesanan->no_sp . ' - ' . $pesanan->tanggal;

        // reset details & search
        $this->details = [];
        $this->obatSearch = [];
        $this->obatResults = [];
        $this->highlightObatIndex = [];

        foreach ($pesanan->details as $i => $d) {
            $obat = $d->obat;

            if ($obat) {
                $this->details[$i]['obat_id']   = $obat->id;
                $this->details[$i]['nama_obat'] = $obat->nama_obat;
                $this->details[$i]['harga']     = $obat->harga_beli ?? 0;
                $this->details[$i]['qty']       = $d->qty ?? 1;
                $this->details[$i]['jumlah']    = ($obat->harga_beli ?? 0) * ($d->qty ?? 1);

                // relasi
                $this->details[$i]['pabrik_id']  = $obat->pabrik_id;
                $this->details[$i]['pabrik']     = $obat->pabrik->nama_pabrik ?? '';

                $this->details[$i]['satuan_id']  = $obat->satuan_id;
                $this->details[$i]['satuan']     = $obat->satuan->nama_satuan ?? '';

                $this->details[$i]['sediaan_id'] = $obat->sediaan_id;
                $this->details[$i]['sediaan']    = $obat->sediaan->nama_sediaan ?? '';

                // default utuh → ikut dari database
                $this->details[$i]['utuhan']     = (bool) $obat->utuh_satuan;
                $this->details[$i]['isi_obat']   = $obat->isi_obat ?? 1;

                // reset search box untuk row ini
                $this->obatSearch[$i]        = $obat->nama_obat;
                $this->obatResults[$i]       = [];
                $this->highlightObatIndex[$i] = 0;
            }
        }

        // sembunyikan list pencarian
        $this->pesananList = [];
        $this->highlightIndex = 0;
    }


    // navigasi keyboard
    public function highlightNext()
    {
        if ($this->highlightIndex + 1 < count($this->pesananList)) {
            $this->highlightIndex++;
        }
    }

    public function highlightPrev()
    {
        if ($this->highlightIndex > 0) {
            $this->highlightIndex--;
        }
    }

    public function selectHighlighted()
    {
        if (isset($this->pesananList[$this->highlightIndex])) {
            $this->selectPesanan($this->pesananList[$this->highlightIndex]->id);
        }
    }

    public $obatSearch = [];        // array untuk tiap baris detail
    public $obatResults = [];       // hasil pencarian obat per baris
    public $highlightObatIndex = []; // highlight keyboard per baris

    public function updatedObatSearch($value, $index)
    {
        if ($value) {
            $this->obatResults[$index] = Obat::where('nama_obat', 'like', "%{$value}%")
                ->orderBy('nama_obat')
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
        if ($obat) {
            $this->details[$index]['obat_id']   = $obat->id;
            $this->details[$index]['nama_obat'] = $obat->nama_obat;
            $this->details[$index]['harga']     = $obat->harga_beli ?? 0;
            $this->details[$index]['qty']       = 1;
            $this->details[$index]['jumlah']    = $obat->harga_beli ?? 0;

            // relasi
            $this->details[$index]['pabrik_id']  = $obat->pabrik_id;
            $this->details[$index]['pabrik']     = $obat->pabrik->nama_pabrik ?? '';

            $this->details[$index]['satuan_id']  = $obat->satuan_id;
            $this->details[$index]['satuan']     = $obat->satuan->nama_satuan ?? '';

            $this->details[$index]['sediaan_id'] = $obat->sediaan_id;
            $this->details[$index]['sediaan']    = $obat->sediaan->nama_sediaan ?? '';

            // default utuh true → gunakan isi_obat
            $this->details[$index]['utuhan']     = (bool) $obat->utuh_satuan;
            $this->details[$index]['isi_obat']   = $obat->isi_obat ?? 1;

            // reset search box di row itu
            $this->obatSearch[$index] = $obat->nama_obat;
            $this->obatResults[$index] = [];
            $this->highlightObatIndex[$index] = 0;
        }
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
            // Kalau utuh
            $this->details[$index]['qty']       = $obat->isi_obat ?? 1;
            $this->details[$index]['satuan_id'] = $obat->satuan_id;
            $this->details[$index]['satuan']    = $obat->satuan->nama_satuan ?? 'SET';
        } else {
            // Kalau tidak utuh
            $this->details[$index]['qty']       = 1;
            $this->details[$index]['satuan_id'] = $obat->sediaan_id;
            $this->details[$index]['satuan']    = $obat->sediaan->nama_sediaan ?? 'PCS';
        }

        // Hitung ulang jumlah
        $qty   = $this->details[$index]['qty'] ?? 1;
        $harga = $this->details[$index]['harga'] ?? 0;
        $this->details[$index]['jumlah'] = $qty * $harga;
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

    public function updatedTanggal($value)
    {
        $this->no_penerimaan = $this->generateNoPenerimaan($value);
    }

    public function generateNoPenerimaan($tanggal)
    {
        if (!$tanggal) return '';

        $date = Carbon::parse($tanggal)->format('ymd');

        $last = Penerimaan::whereDate('tanggal', $tanggal)
            ->orderBy('no_penerimaan', 'desc')
            ->first();

        if ($last && strlen($last->no_penerimaan) >= 10) {
            $number = intval(substr($last->no_penerimaan, 6)) + 1;
        } else {
            $number = 1;
        }

        return $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    public function updatedJenisBayar($value)
    {
        $this->setTenorAndJatuhTempo();
    }

    private function setTenorAndJatuhTempo()
    {
        if ($this->jenis_bayar === 'KREDIT') {
            $this->tenor = 30;
            $this->jatuh_tempo = \Carbon\Carbon::parse($this->tanggal)->addDays(30)->format('Y-m-d');
        } elseif ($this->jenis_bayar === 'CASH') {
            $this->tenor = 0;
            $this->jatuh_tempo = \Carbon\Carbon::parse($this->tanggal)->format('Y-m-d');
        } else {
            $this->tenor = null;
            $this->jatuh_tempo = null;
        }
    }
}
