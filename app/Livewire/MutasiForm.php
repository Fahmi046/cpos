<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Obat;
use App\Models\Mutasi;
use App\Models\Outlet;
use Livewire\Component;
use App\Models\KartuStok;
use App\Models\Permintaan;
use App\Models\StokOutlet;
use App\Models\MutasiDetail;
use Illuminate\Http\Request;
use App\Models\PenerimaanDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public $permintaan_id;
    public $permintaan;


    public $outlets = [];

    public function mount(Request $request)
    {
        $this->tanggal = date('Y-m-d');
        $this->outlets = \App\Models\Outlet::all();

        // Ambil permintaan_id dari query string
        $this->permintaan_id = $request->get('permintaan_id');

        if ($this->permintaan_id) {
            $this->loadFromPermintaan($this->permintaan_id);
        } else {
            // default kalau tanpa permintaan
            $this->no_mutasi = $this->generateNoMT();
            $this->details = [$this->emptyDetailRow()];
        }
    }

    /**
     * Preload data dari permintaan ke form mutasi
     */
    private function loadFromPermintaan($permintaan_id)
    {
        $this->permintaan = \App\Models\Permintaan::with([
            'details' => function ($q) {
                $q->where('status', 'pending'); // hanya detail pending
            },
            'details.obat.satuan',
            'outlet'
        ])->findOrFail($permintaan_id);

        // Isi header mutasi
        $this->no_mutasi    = $this->generateNoMT();
        $this->tanggal      = $this->permintaan->tanggal;
        $this->outlet_id    = $this->permintaan->outlet_id;
        $this->keterangan   = $this->permintaan->keterangan;
        $this->searchoutlet = $this->permintaan->outlet->nama_outlet ?? '';

        // Reset details & pencarian obat
        $this->details    = [];
        $this->obatSearch = [];

        foreach ($this->permintaan->details as $detail) {
            $obat = $detail->obat;
            if (!$obat) continue;

            $qtyMinta = $detail->qty_sisa > 0
                ? (int) $detail->qty_sisa
                : (int) ($detail->qty_minta ?? 0);

            $sisaQty = $qtyMinta;

            // ✅ Ambil stok batch urut ED terdekat (PAKAI kolom masuk & keluar)
            $stokList = DB::table('kartu_stok as ks')
                ->select(
                    'ks.batch',
                    'ks.ed',
                    DB::raw('SUM(ks.masuk) - SUM(ks.keluar) AS stok')
                )
                ->where('ks.obat_id', $obat->id)
                ->whereDate('ks.ed', '>=', now())   // hanya batch yg belum expired
                ->groupBy('ks.batch', 'ks.ed')
                ->havingRaw('stok > 0')             // hanya batch dengan stok > 0
                ->orderBy('ks.ed', 'asc')           // urut ED terdekat
                ->get();

            // Loop stok terdekat
            foreach ($stokList as $stokData) {
                if ($sisaQty <= 0) break;

                $qtyAmbil = min($sisaQty, $stokData->stok);
                $sisaQty -= $qtyAmbil;

                $this->details[] = [
                    'obat_id'   => $obat->id,
                    'pabrik'    => $obat->pabrik->nama_pabrik ?? '',
                    'utuh'      => isset($detail->utuhan)
                        ? (bool) $detail->utuhan
                        : (($obat->isi_obat ?? 1) > 1),
                    'satuan_id' => $obat->satuan->id ?? null,
                    'satuan'    => $obat->satuan->nama_satuan ?? '',
                    'isi_obat'  => $obat->isi_obat ?? 0,
                    'harga'     => $detail->harga ?? 0,
                    'ed'        => $stokData->ed,
                    'batch'     => $stokData->batch,
                    'stok'      => (int) $stokData->stok,
                    'qty'       => (int) $qtyAmbil,
                    'permintaan_detail_id' => $detail->id,
                ];

                $this->obatSearch[] = $obat->nama_obat;
            }

            // Kalau stok di semua batch belum cukup → buat detail "pending"
            if ($sisaQty > 0) {
                $this->details[] = [
                    'obat_id'   => $obat->id,
                    'pabrik'    => $obat->pabrik->nama_pabrik ?? '',
                    'utuh'      => isset($detail->utuhan)
                        ? (bool) $detail->utuhan
                        : (($obat->isi_obat ?? 1) > 1),
                    'satuan_id' => $obat->satuan->id ?? null,
                    'satuan'    => $obat->satuan->nama_satuan ?? '',
                    'isi_obat'  => $obat->isi_obat ?? 0,
                    'harga'     => $detail->harga ?? 0,
                    'ed'        => null,
                    'batch'     => null,
                    'stok'      => 0,
                    'qty'       => $sisaQty, // sisa yang belum bisa dipenuhi
                    'permintaan_detail_id' => $detail->id,
                ];

                $this->obatSearch[] = $obat->nama_obat;
            }
        }
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
            'utuh'       => false,
            'permintaan_detail_id' => null, // utuhan atau tidak
        ];
    }
    public function save()
    {
        if ($this->obat_id) {
            $this->addDetail();
        }

        $this->validate([
            'no_mutasi'         => 'required|unique:mutasi,no_mutasi,' . ($this->mutasi_id ?? 'NULL') . ',id',
            'tanggal'           => 'required|date',
            'outlet_id'         => 'required|exists:outlets,id',
            'details.*.obat_id' => 'required|exists:obat,id',
            'details.*.qty'     => 'required|integer|min:1',
        ]);

        DB::transaction(function () {

            // =====================
            // Jika Mutasi dari Permintaan
            // =====================
            if ($this->permintaan_id) {
                $permintaan = \App\Models\Permintaan::with('details')->find($this->permintaan_id);

                foreach ($this->details as $d) {
                    $pd = $permintaan->details->firstWhere('obat_id', $d['obat_id']);
                    if (!$pd) continue;

                    $stok = (int) \App\Models\KartuStok::where('obat_id', $d['obat_id'])
                        ->sum(DB::raw("masuk - keluar"));

                    $qtyMinta = (int) ($d['qty'] ?? 1);

                    if ($stok >= $qtyMinta) {
                        $pd->qty_mutasi = $qtyMinta;
                        $pd->qty_sisa   = 0;
                        $pd->status     = 'selesai';
                    } elseif ($stok > 0) {
                        $pd->qty_mutasi = $stok;
                        $pd->qty_sisa   = $qtyMinta - $stok;
                        $pd->status     = 'pending';
                    } else {
                        $pd->qty_mutasi = 0;
                        $pd->qty_sisa   = $qtyMinta;
                        $pd->status     = 'pending';
                    }

                    $pd->save();
                }

                $semuaTerpenuhi = $permintaan->details->every(fn($det) => $det->status === 'selesai');
                $adaTerpenuhi   = $permintaan->details->contains(fn($det) => $det->status === 'pending');
                $permintaan->status = $semuaTerpenuhi ? 'selesai' : ($adaTerpenuhi ? 'sebagian' : 'baru');
                $permintaan->save();
            }

            // =====================
            // Cek ada stok valid
            // =====================
            $adaStokValid = false;
            foreach ($this->details as $d) {
                $stok = (int) \App\Models\KartuStok::where('obat_id', $d['obat_id'])
                    ->sum(DB::raw("masuk - keluar"));

                if ($stok > 0) {
                    $adaStokValid = true;
                    break;
                }
            }

            if (!$adaStokValid) {
                DB::rollBack();
                session()->flash('error', 'Semua stok kosong, mutasi tidak dibuat.');
                return;
            }

            // =====================
            // Create / Update Mutasi
            // =====================
            if ($this->mutasi_id) {
                $mutasi = \App\Models\Mutasi::findOrFail($this->mutasi_id);
                \App\Models\StokOutlet::where('mutasi_id', $mutasi->id)->delete();
                \App\Models\KartuStok::where('mutasi_id', $mutasi->id)->delete();
                $mutasi->details()->delete();

                $mutasi->update([
                    'no_mutasi'     => $this->no_mutasi,
                    'tanggal'       => $this->tanggal,
                    'outlet_id'     => $this->outlet_id,
                    'keterangan'    => $this->keterangan,
                    'permintaan_id' => $this->permintaan_id,
                ]);
            } else {
                $mutasi = \App\Models\Mutasi::create([
                    'no_mutasi'     => $this->no_mutasi,
                    'tanggal'       => $this->tanggal,
                    'outlet_id'     => $this->outlet_id,
                    'keterangan'    => $this->keterangan,
                    'permintaan_id' => $this->permintaan_id,
                ]);
            }

            // =====================
            // Simpan Detail & KartuStok
            // =====================
            foreach ($this->details as $d) {
                $obat = \App\Models\Obat::find($d['obat_id']);
                if (!$obat) continue;

                $stok = (int) \App\Models\KartuStok::where('obat_id', $obat->id)
                    ->sum(DB::raw("masuk - keluar"));

                $qtySimpan = min($stok, (int)($d['qty'] ?? 1));
                if ($qtySimpan <= 0) continue;

                $detail = $mutasi->details()->create([
                    'obat_id'    => $obat->id,
                    'pabrik_id'  => $obat->pabrik_id ?? 1,
                    'satuan_id'  => $obat->satuan_id ?? 1,
                    'sediaan_id' => $obat->sediaan_id ?? 1,
                    'qty'        => $qtySimpan,
                    'harga'      => $d['harga'] ?? 0,
                    'batch'      => $d['batch'] ?? null,
                    'ed'         => $d['ed'] ?? null,
                    'jumlah'     => $qtySimpan * ($d['harga'] ?? 0),
                    'utuhan'     => $d['utuh'] ?? 0,
                    'permintaan_detail_id' => $d['permintaan_detail_id'] ?? null,
                ]);

                // =====================
                // Buat KartuStok keluar & hitung saldo_akhir
                // =====================
                $saldoAkhir = $stok - $qtySimpan;

                $kartu = \App\Models\KartuStok::create([
                    'obat_id'          => $obat->id,
                    'satuan_id'        => $obat->satuan_id ?? 1,
                    'sediaan_id'       => $obat->sediaan_id ?? 1,
                    'pabrik_id'        => $obat->pabrik_id ?? 1,
                    'mutasi_id'        => $mutasi->id,
                    'mutasi_detail_id' => $detail->id,
                    'jenis'            => 'keluar',
                    'masuk'            => 0,
                    'keluar'           => $qtySimpan,
                    'batch'            => $detail->batch,
                    'ed'               => $detail->ed,
                    'tanggal'          => $mutasi->tanggal,
                    'keterangan'       => 'Mutasi',
                    'saldo_akhir'      => $saldoAkhir,
                ]);

                // =====================
                // Stok outlet masuk
                // =====================
                if ($mutasi->outlet_id) {
                    \App\Models\StokOutlet::recordMovement([
                        'outlet_id'        => $mutasi->outlet_id,
                        'obat_id'          => $obat->id,
                        'batch'            => $detail->batch,
                        'ed'               => $detail->ed,
                        'jenis'            => 'masuk',
                        'qty'              => $qtySimpan,
                        'tanggal'          => $mutasi->tanggal,
                        'satuan_id'        => $obat->satuan_id ?? 1,
                        'sediaan_id'       => $obat->sediaan_id ?? 1,
                        'pabrik_id'        => $obat->pabrik_id ?? 1,
                        'mutasi_id'        => $mutasi->id,
                        'mutasi_detail_id' => $detail->id,
                        'keterangan'       => 'Mutasi',
                    ]);
                }
            }
        });

        session()->flash('message', $this->mutasi_id ? 'Mutasi berhasil diperbarui!' : 'Mutasi berhasil disimpan!');
        $this->resetForm();
        $this->dispatch('refreshTable');
        $this->dispatch('focus-tanggal');
        $this->no_mutasi = $this->generateNoMT();

        if ($this->permintaan_id) {
            return redirect('/permintaan');
        }
    }


    protected $listeners = [
        'refreshKodeMutasi' => 'updatedTanggal',
    ];


    private function resetForm()
    {
        $this->mutasi_id   = null;
        $this->no_mutasi   = $this->generateNoMT();
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
        return view('livewire.mutasi-form');
    }

    protected $branchCode = 'GUDANG';

    public function generateNoMT()
    {
        return DB::transaction(function () {
            $tanggal = Carbon::parse($this->tanggal);
            $year    = $tanggal->format('Y');
            $month   = $this->monthToRoman((int)$tanggal->format('n'));

            // lock data mutasi di tahun berjalan
            $last = \App\Models\Mutasi::whereYear('tanggal', $year)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            // ambil nomor terakhir, kalau belum ada mulai dari 1
            if ($last && preg_match('/MT-(\d+)\//', $last->no_mutasi, $matches)) {
                $count = (int) $matches[1] + 1;
            } else {
                $count = 1;
            }

            $seq = str_pad($count, 4, '0', STR_PAD_LEFT);

            return "MT-{$seq}/{$month}/{$year}";
        });
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

        // Cari detail penerimaan terbaru untuk obat + batch + ed
        $penerimaanDetail = PenerimaanDetail::where('obat_id', $obat_id)
            ->when($batch, fn($q) => $q->where('batch', $batch))
            ->when($ed, fn($q) => $q->where('ed', $ed))
            ->latest('id')
            ->first();

        $this->details[$index]['obat_id']   = $obat->id;
        $this->details[$index]['nama_obat'] = $obat->nama_obat;
        $this->details[$index]['isi_obat']  = $obat->isi_obat ?? 1;

        // harga → ambil dari penerimaan detail kalau ada
        $harga = $penerimaanDetail->harga ?? $obat->harga_beli ?? 0;
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

        // batch, ED, stok → pakai dari penerimaanDetail kalau ada
        $this->details[$index]['batch'] = $penerimaanDetail->batch ?? $batch ?? '';
        $this->details[$index]['ed']    = $penerimaanDetail->ed ?? $ed ?? null;
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
