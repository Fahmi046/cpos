<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Obat;
use App\Models\Outlet;
use Livewire\Component;
use App\Models\Permintaan;
use Illuminate\Validation\Rule;
use App\Models\PermintaanDetail;
use Illuminate\Support\Facades\DB;

class PermintaanForm extends Component
{
    public $permintaan_id;
    public $no_permintaan;
    public $tanggal;
    public $outlet_id;
    public $keterangan;

    public $details = []; // array detail permintaan
    public $obat_id;
    public $qty = 1;
    public $batch;
    public $ed;

    public $outlets = [];

    public function rules()
    {
        return [
            'no_permintaan'         => ['required', Rule::unique('permintaan', 'no_permintaan')->ignore($this->permintaan_id)],
            'tanggal'               => 'required|date',
            'outlet_id'             => 'required|exists:outlets,id',
            'details'               => 'required|array|min:1',
            'details.*.obat_id'     => 'required|exists:obat,id',
            'details.*.qty'         => 'required|integer|min:1',
            // opsional: kamu bisa tambahkan validasi untuk harga, batch, ed jika perlu
        ];
    }


    public function mount()
    {
        $this->tanggal = date('Y-m-d');;
        $this->details = [$this->emptyDetailRow()];
        $this->outlets = \App\Models\Outlet::all();
        $this->no_permintaan = $this->generateNoMO();
    }

    protected function emptyDetailRow(): array
    {
        return [
            'obat_id'    => null,
            'pabrik_id'  => null,
            'satuan_id'  => null,
            'sediaan_id' => null,
            'qty'        => 1,
            'ed' => date('Y-m-d'), // 🔹 default tanggal sekarang
            'batch'      => '',
            'utuh'       => false, // utuhan atau tidak
        ];
    }
    public function save()
    {
        // tambahkan baris terakhir bila user mengetik di input utama tanpa klik "Tambah"
        if ($this->obat_id) {
            $this->addDetail();
        }

        // validasi memakai rules() di atas
        $validated = $this->validate();

        DB::transaction(function () {
            if ($this->permintaan_id) {
                $permintaan = Permintaan::findOrFail($this->permintaan_id);

                // hapus detail lama (sederhana) lalu insert ulang
                $permintaan->details()->delete();

                $permintaan->update([
                    'no_permintaan' => $this->no_permintaan,
                    'tanggal'       => $this->tanggal,
                    'outlet_id'     => $this->outlet_id,
                    'keterangan'    => $this->keterangan,
                    'status'        => 'pending', // tetap pending setelah update
                ]);
            } else {
                $permintaan = Permintaan::create([
                    'no_permintaan' => $this->no_permintaan,
                    'tanggal'       => $this->tanggal,
                    'outlet_id'     => $this->outlet_id,
                    'keterangan'    => $this->keterangan,
                    'status'        => 'pending',
                ]);
            }

            // simpan detail permintaan (status default pending)
            foreach ($this->details as $d) {
                // pastikan obat masih ada
                $obat = Obat::find($d['obat_id']);
                if (! $obat) continue;

                $permintaan->details()->create([
                    'obat_id'    => $obat->id,
                    'pabrik_id'  => $obat->pabrik_id ?? null,
                    'satuan_id'  => $obat->satuan_id ?? null,
                    'sediaan_id' => $obat->sediaan_id ?? null,
                    'qty_minta'  => $d['qty'] ?? 1,
                    'qty_mutasi' => 0,
                    'harga'      => $d['harga'] ?? 0,
                    'batch'      => $d['batch'] ?? null,
                    'ed'         => $d['ed'] ?? null,
                    'utuhan'     => $d['utuhan'] ?? 0,
                    'status'     => $d['status'] ?? 'pending',
                ]);
            }
        });

        session()->flash('message', $this->permintaan_id ? 'Permintaan berhasil diperbarui!' : 'Permintaan berhasil disimpan!');
        $this->resetForm();
        $this->dispatch('refreshTable');
    }




    private function resetForm()
    {
        $this->permintaan_id   = null;
        $this->no_permintaan   = $this->generateNoMO();
        $this->tanggal     = date('Y-m-d');
        $this->outlet_id   = null;
        $this->keterangan  = '';

        // reset outlet search
        $this->searchoutlet   = '';
        $this->outletResults  = [];
        $this->highlightIndex = 0;

        // reset detail baris
        $this->details             = [$this->emptyDetailRow()];
        $this->obatSearch          = [''];
        $this->obatResults         = [[]];
        $this->highlightObatIndex  = [0];

        // reset inputan satuan
        $this->obat_id = null;
        $this->qty     = 1;
        $this->batch   = '';
        $this->ed      = '';
    }



    public function render()
    {
        return view('livewire.permintaan-form');
    }

    protected $branchCode = 'GUDANG';

    public function generateNoMO()
    {
        $tanggal = Carbon::parse($this->tanggal);
        $year = $tanggal->format('Y');
        $month = $this->monthToRoman((int)$tanggal->format('n'));

        // Hitung urutan MO tahun ini
        $count = permintaan::whereYear('tanggal', $year)->count() + 1;
        $seq = str_pad($count, 4, '0', STR_PAD_LEFT);

        // Format MO
        return "MO-{$seq}/{$month}/{$year}";
    }

    public function updatedTanggal()
    {
        $this->no_permintaan = $this->generateNoMO();
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
    public function incrementHighlight(): void
    {
        if ($this->highlightIndex < count($this->outletResults) - 1) {
            $this->highlightIndex++;
        }
    }

    public function decrementHighlight(): void
    {
        if ($this->highlightIndex > 0) {
            $this->highlightIndex--;
        }
    }

    public function selectHighlighted(): void
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
            $this->obatResults[$index] = DB::table('kartu_stok as ks')
                ->join('obat as o', 'ks.obat_id', '=', 'o.id')
                ->select(
                    'ks.obat_id',
                    'o.nama_obat',
                    'ks.batch',
                    'ks.ed',
                    DB::raw("
            COALESCE(SUM(CASE WHEN ks.jenis = 'masuk' THEN ks.qty ELSE 0 END), 0)
            - COALESCE(SUM(CASE WHEN ks.jenis = 'keluar' THEN ks.qty ELSE 0 END), 0) as stok
        ")
                )
                ->where('o.nama_obat', 'like', '%' . $value . '%')
                ->groupBy('ks.obat_id', 'ks.batch', 'ks.ed', 'o.nama_obat')
                ->having('stok', '>', 0)
                ->orderBy('ks.ed', 'asc')
                ->limit(10)
                ->get();
        } else {
            $this->obatResults[$index] = [];
        }

        $this->highlightObatIndex[$index] = 0;
    }


    public function selectObat($index, $obat_id, $batch = null, $ed = null, $stok = 0)
    {
        $obat = Obat::with(['satuan', 'sediaan', 'pabrik'])->find($obat_id);
        if (!$obat) return;

        // Cari detail permintaan terbaru untuk obat + batch + ed
        $PermintaanDetail = PermintaanDetail::where('obat_id', $obat_id)
            ->when($batch, fn($q) => $q->where('batch', $batch))
            ->when($ed, fn($q) => $q->where('ed', $ed))
            ->latest('id')
            ->first();

        $this->details[$index]['obat_id']   = $obat->id;
        $this->details[$index]['nama_obat'] = $obat->nama_obat;
        $this->details[$index]['isi_obat']  = $obat->isi_obat ?? 1;

        // harga → ambil dari permintaan detail kalau ada
        $harga = $PermintaanDetail->harga ?? $obat->harga_beli ?? 0;
        $this->details[$index]['harga']  = $harga;
        $this->details[$index]['qty']    = 1;
        $this->details[$index]['jumlah'] = $harga;

        // relasi
        $this->details[$index]['pabrik_id'] = $obat->pabrik_id;
        $this->details[$index]['pabrik']    = $obat->pabrik->nama_pabrik ?? '';

        $this->details[$index]['satuan_id'] = $obat->satuan_id;
        $this->details[$index]['satuan']    = $obat->satuan->nama_satuan ?? '';

        $this->details[$index]['sediaan_id'] = $obat->sediaan_id;
        $this->details[$index]['sediaan']    = $obat->sediaan->nama_sediaan ?? '';

        // batch, ED, stok → pakai dari PermintaanDetail kalau ada
        $this->details[$index]['batch'] = $PermintaanDetail->batch ?? $batch ?? '';
        $this->details[$index]['ed']    = $PermintaanDetail->ed ?? $ed ?? null;
        $this->details[$index]['stok']  = $stok ?? 0;

        // default utuh
        $this->details[$index]['utuh'] = ($obat->isi_obat ?? 1) > 1;

        // reset search
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
        if (!isset($this->highlightObatIndex[$index])) {
            $this->highlightObatIndex[$index] = 0;
        }

        if (isset($this->obatResults[$index][$this->highlightObatIndex[$index] + 1])) {
            $this->highlightObatIndex[$index]++;
        }
    }

    public function highlightPrevObat($index)
    {
        if (!isset($this->highlightObatIndex[$index])) {
            $this->highlightObatIndex[$index] = 0;
        }

        if ($this->highlightObatIndex[$index] > 0) {
            $this->highlightObatIndex[$index]--;
        }
    }

    public function selectHighlightedObat($index)
    {
        // Cek apakah hasil pencarian ada
        if (empty($this->obatResults[$index])) {
            return;
        }

        // Ambil highlight index dengan fallback 0
        $highlight = $this->highlightObatIndex[$index] ?? 0;

        // Cek apakah highlight valid
        if (!isset($this->obatResults[$index][$highlight])) {
            return;
        }

        $ks = $this->obatResults[$index][$highlight];

        $this->selectObat(
            $index,
            $ks->obat_id,
            $ks->batch,
            $ks->ed,
            $ks->stok
        );
    }

    public function addDetail()
    {
        $this->details[] = $this->emptyDetailRow();
        $this->obatSearch[] = '';
        $this->obatResults[] = [];
        $this->highlightObatIndex[] = 0;

        $lastIndex = count($this->details) - 1;

        $this->dispatch('focus-obat', index: $lastIndex);
    }

    public function removeDetail($index)
    {
        if (isset($this->details[$index])) {
            unset($this->details[$index]);
            $this->details = array_values($this->details);
        }
    }
}
