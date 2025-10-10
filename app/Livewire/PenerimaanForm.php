<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Pesanan;
use Livewire\Component;
use App\Models\Kreditur;
use App\Models\Penerimaan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\{BentukSediaan, Obat, Pabrik, Satuan, SatuanObat, Sediaan};


class PenerimaanForm extends Component
{
    public $penerimaan_id;

    // Header Penerimaan
    public $pesanan_id, $tanggal, $no_penerimaan, $jenis_bayar, $kreditur_id, $kreditur_nama;
    public $no_faktur, $tenor, $jatuh_tempo, $jenis_ppn;

    // DETAIL PENERIMAAN (<<— inilah yang dimaksud $details)
    public $details = [];   // <- WAJIB ada supaya tidak "Undefined variable $details"

    public function mount(?int $penerimaan_id = null): void
    {
        $this->resetForm($penerimaan_id);
    }


    private function resetForm(?int $penerimaan_id = null)
    {
        $this->penerimaan_id = $penerimaan_id;

        // minimal 1 baris kosong detail
        $this->details = [$this->emptyDetailRow()];

        // reset autocomplete obat
        $this->obatSearch = [''];
        $this->obatResults = [[]];
        $this->highlightObatIndex = [0];

        // reset search pesanan
        $this->no_sp = '';
        $this->pesananList = [];
        $this->highlightIndex = 0;

        // 🔹 DEFAULT HEADER
        $this->tanggal       = Carbon::now()->format('Y-m-d'); // hari ini
        $this->jenis_bayar   = 'Kredit';
        $this->jenis_ppn     = 'non';
        $this->kreditur_id   = null;
        $this->kreditur_nama = '';
        $this->no_faktur     = '';
        $this->tenor         = 30;
        $this->jatuh_tempo   = Carbon::now()->addDays(30)->format('Y-m-d');

        // 🔹 NO PENERIMAAN OTOMATIS
        $this->no_penerimaan = $this->generateNoPenerimaan($this->tanggal);

        // 🔹 Reset ringkasan
        $this->dpp   = 0;
        $this->ppn   = 0;
        $this->total = 0;
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

    public function addDetail()
    {
        $this->details[] = $this->emptyDetailRow();
        $this->obatSearch[] = '';
        $this->obatResults[] = [];
        $this->highlightObatIndex[] = 0;

        $lastIndex = count($this->details) - 1;

        $this->dispatch('focus-row', index: $lastIndex);
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
            'pesanan_id' => 'nullable|exists:pesanan,id',
            'tanggal'    => 'required|date',
            'jenis_bayar' => 'required|in:Cash,Kredit,Konsinyasi',

            'details'                 => 'required|array|min:1',
            'details.*.obat_id'       => 'required|exists:obat,id',
            'details.*.pabrik_id'     => 'nullable|exists:pabrik,id',
            'details.*.satuan_id'     => 'required|exists:satuan_obat,id',
            'details.*.sediaan_id'    => 'nullable|exists:bentuk_sediaans,id',
            'details.*.qty'           => 'required|numeric|min:1',
            'details.*.ed'            => 'nullable|date',
            'details.*.batch'         => 'nullable|string|max:50',
            'details.*.disc1'         => 'nullable|numeric|min:0',
            'details.*.disc2'         => 'nullable|numeric|min:0',
            'details.*.disc3'         => 'nullable|numeric|min:0',
            'details.*.utuh'          => 'nullable|boolean',
        ]);

        try {
            // 🔍 Debug awal
            Log::info('Data sebelum simpan:', [
                'header' => [
                    'pesanan_id'   => $this->pesanan_id,
                    'tanggal'      => $this->tanggal,
                    'jenis_bayar'  => $this->jenis_bayar,
                    'kreditur_id'  => $this->kreditur_id,
                    'no_faktur'    => $this->no_faktur,
                    'tenor'        => $this->tenor,
                    'jatuh_tempo'  => $this->jatuh_tempo,
                    'jenis_ppn'    => $this->jenis_ppn,
                    'dpp'          => $this->dpp,
                    'ppn'          => $this->ppn,
                    'total'        => $this->total,
                ],
                'details' => $this->details,
            ]);

            DB::transaction(function () {
                $penerimaan = Penerimaan::updateOrCreate(
                    ['id' => $this->penerimaan_id],
                    [
                        'pesanan_id' => $this->pesanan_id ?: null,
                        'tanggal'       => $this->tanggal,
                        'no_penerimaan' => $this->no_penerimaan,
                        'jenis_bayar'   => $this->jenis_bayar,
                        'kreditur_id'   => $this->kreditur_id ?? null,
                        'no_faktur'     => $this->no_faktur,
                        'tenor'         => $this->tenor ?? null,
                        'jatuh_tempo'   => $this->jatuh_tempo ?? null,
                        'jenis_ppn'     => $this->jenis_ppn,
                        'dpp'           => $this->dpp,
                        'ppn'           => $this->ppn,
                        'total'         => $this->total,
                    ]
                );

                foreach ($this->details as $row) {
                    // 🔍 Debug per detail
                    Log::info('Simpan detail penerimaan:', $row);

                    $penerimaan->details()->updateOrCreate(
                        ['id' => $row['id'] ?? null],
                        [
                            'obat_id'    => $row['obat_id'],
                            'pabrik_id'  => $row['pabrik_id'] ?: null,
                            'satuan_id'  => $row['satuan_id'] ?: null,
                            'sediaan_id' => $row['sediaan_id'] ?: null,
                            'qty'        => $row['qty'] ?? 0,
                            'ed'         => $row['ed'] ?: null,
                            'batch'      => $row['batch'] ?: null,
                            'disc1'      => $row['disc1'] ?? 0,
                            'disc2'      => $row['disc2'] ?? 0,
                            'disc3'      => $row['disc3'] ?? 0,
                            'harga'      => $row['harga'] ?? 0,
                            'subtotal'   => $row['subtotal'] ?? 0,
                            'utuh'       => (bool) ($row['utuh'] ?? false),
                        ]
                    );
                }

                // ✅ Update status pesanan menjadi 'diterima'
                if ($this->pesanan_id) {
                    \App\Models\Pesanan::where('id', $this->pesanan_id)
                        ->update(['status' => 'diterima']);
                }
            });

            session()->flash('success', 'Data berhasil disimpan.');
            $this->resetForm();
            $this->dispatch('refreshTable');
            $this->dispatch('focus-nosp');
            return $this->redirectRoute('penerimaan.index');
        } catch (\Throwable $e) {
            Log::error('Gagal simpan penerimaan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
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
            $this->pesananList = Pesanan::where(function ($q) {
                $q->where('no_sp', 'like', "%{$this->search}%")
                    ->orWhere('tanggal', 'like', "%{$this->search}%");
            })
                ->whereNotIn('id', function ($q2) {
                    $q2->select('pesanan_id')->from('penerimaan')->whereNotNull('pesanan_id');
                })
                ->orderBy('tanggal', 'desc')
                ->limit(5)
                ->get();
        } else {
            $this->pesananList = [];
        }

        $this->highlightIndex = 0; // reset highlight saat search berubah
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
            // default utuh true kalau isi_obat > 1
            $this->details[$index]['utuh']      = ($obat->isi_obat ?? 1) > 1 ? true : false;
            $this->details[$index]['isi_obat']   = $obat->isi_obat ?? 1;

            // reset search box di row itu
            $this->obatSearch[$index] = $obat->nama_obat;
            $this->obatResults[$index] = [];
            $this->highlightObatIndex[$index] = 0;
            $this->hitungRingkasan();
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

    public function updatedTanggal($value)
    {
        $this->no_penerimaan = $this->generateNoPenerimaan($value);
    }

    public function generateNoPenerimaan($tanggal = null)
    {
        // kalau tidak dikirim, default pakai hari ini
        $tanggal = $tanggal ?? Carbon::today()->toDateString();

        return DB::transaction(function () use ($tanggal) {
            $date = Carbon::parse($tanggal)->format('ymd');

            $last = Penerimaan::whereDate('tanggal', $tanggal)
                ->lockForUpdate()
                ->orderByDesc('no_penerimaan')
                ->first();

            $number = ($last && strlen($last->no_penerimaan) >= 10)
                ? intval(substr($last->no_penerimaan, 6)) + 1
                : 1;

            return $date . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }


    public function updatedJenisBayar($value)
    {
        $this->setTenorAndJatuhTempo();
    }

    private function setTenorAndJatuhTempo()
    {
        if ($this->jenis_bayar === 'Kredit') {
            $this->tenor = 30;
            $this->jatuh_tempo = \Carbon\Carbon::parse($this->tanggal)
                ->addDays(30)
                ->format('Y-m-d');
        } elseif ($this->jenis_bayar === 'Cash') {
            $this->tenor = 0;
            $this->jatuh_tempo = \Carbon\Carbon::parse($this->tanggal)
                ->format('Y-m-d');
        } elseif ($this->jenis_bayar === 'Konsinyasi') {
            // Konsinyasi default tanpa tenor, jatuh tempo bisa diatur manual
            $this->tenor = null;
            $this->jatuh_tempo = null;
        } else {
            $this->tenor = null;
            $this->jatuh_tempo = null;
        }
    }

    public $dpp = 0, $ppn = 0, $total = 0;

    // Dipanggil kalau jenis_ppn berubah
    public function updatedJenisPpn()
    {
        $this->hitungRingkasan();
    }

    // Dipanggil kalau detail berubah
    public function updatedDetails($value, $key)
    {
        // $key contoh: "0.qty" atau "1.nama_obat"
        if (str_contains($key, 'nama_obat')) {
            $index = explode('.', $key)[0]; // ambil index dari details
            $this->obatResults[$index] = Obat::where('nama_obat', 'like', "%{$value}%")
                ->limit(10)
                ->get()
                ->toArray();
        }

        $this->hitungJumlahPerRow();
        $this->hitungRingkasan();
    }



    private function hitungRingkasan()
    {
        $dpp_raw = 0;

        // Hitung jumlah per row dulu
        foreach ($this->details as $i => $row) {
            $harga = $row['harga'] ?? 0;
            $qty   = $row['qty'] ?? 0;
            $isi_obat   = $row['isi_obat'] ?? 0;
            $disc1 = $row['disc1'] ?? 0;
            $disc2 = $row['disc2'] ?? 0;
            $disc3 = $row['disc3'] ?? 0;

            // Hitung subtotal dengan diskon
            $qty_all   = $qty * $isi_obat;
            $subtotal = $harga * $qty_all;
            $totalDisc = $subtotal * ($disc1 + $disc2 + $disc3) / 100;
            $subtotal -= $totalDisc;

            // Simpan kembali ke row
            $this->details[$i]['jumlah'] = $subtotal;

            // Tambahkan ke DPP raw
            $dpp_raw += $subtotal;
        }

        // Hitung DPP, PPN, dan Total berdasarkan jenis_ppn
        switch (strtolower($this->jenis_ppn)) {
            case 'non':
                $dpp   = round($dpp_raw);
                $ppn   = 0;
                $total = $dpp;
                break;

            case 'include':
                $ppn   = round($dpp_raw * 11 / 111);
                $dpp   = round($dpp_raw - $ppn);
                $total = round($dpp_raw);
                break;

            case 'exclude':
                $ppn   = round($dpp_raw * 11 / 100);
                $dpp   = round($dpp_raw);
                $total = round($dpp_raw + $ppn);
                break;

            default:
                $dpp   = round($dpp_raw);
                $ppn   = 0;
                $total = $dpp;
                break;
        }

        // Simpan ke properti Livewire
        $this->dpp   = $dpp;
        $this->ppn   = $ppn;
        $this->total = $total;
    }



    public function updateHarga($index, $value)
    {
        // Hilangkan titik ribuan supaya tersimpan angka bersih
        $clean = preg_replace('/[^\d]/', '', $value);
        $harga = $clean === '' ? 0 : (int) $clean;

        $this->details[$index]['harga'] = $harga;

        // Hitung ulang jumlah (qty * harga)
        $qty = $this->details[$index]['qty'] ?? 0;
        $this->details[$index]['jumlah'] = $qty * $harga;

        // Hitung ringkasan DPP, PPN, TOTAL
        $this->hitungRingkasan();
    }
    private function hitungJumlahPerRow()
    {
        foreach ($this->details as $i => $detail) {
            $harga = $detail['harga'] ?? 0;
            $isi_obat = $detail['isi_obat'] ?? 0;
            $qty   = $detail['qty'] ?? 1;
            $qty_all = $isi_obat + $qty;

            $disc1 = $detail['disc1'] ?? 0;
            $disc2 = $detail['disc2'] ?? 0;
            $disc3 = $detail['disc3'] ?? 0;

            $totalDisc = $harga * ($disc1 + $disc2 + $disc3) / 100;
            $this->details[$i]['jumlah'] = ($harga * $qty_all) - $totalDisc;
        }
    }

    protected $listeners = [
        'refreshKodepenerimaan' => 'refreshNoPenerimaan',
        'edit-penerimaan' => 'edit'
    ];
    public function refreshNoPenerimaan()
    {
        // pakai tanggal yang ada di form, atau hari ini jika null
        $tanggal = $this->tanggal ?? Carbon::today()->toDateString();
        $this->no_penerimaan = $this->generateNoPenerimaan($tanggal);
    }


    public function edit($id)
    {
        $penerimaan = Penerimaan::with(['pesanan', 'details.obat', 'kreditur'])->findOrFail($id);

        // Header penerimaan
        $this->penerimaan_id = $penerimaan->id;
        $this->search = $penerimaan->pesanan->no_sp . ' - ' . $penerimaan->pesanan->tanggal;
        $this->no_penerimaan = $penerimaan->no_penerimaan;
        $this->pesanan_id    = $penerimaan->pesanan_id;
        $this->tanggal = $penerimaan->tanggal
            ? Carbon::parse($penerimaan->tanggal)->format('Y-m-d')
            : null;
        $this->jenis_ppn     = $penerimaan->jenis_ppn;
        $this->no_faktur     = $penerimaan->no_faktur;
        $this->jenis_bayar   = $penerimaan->jenis_bayar;
        $this->tenor         = $penerimaan->tenor;
        $this->jatuh_tempo = $penerimaan->jatuh_tempo
            ? Carbon::parse($penerimaan->jatuh_tempo)->format('Y-m-d')
            : null;
        $this->kreditur_id   = $penerimaan->kreditur_id;
        $this->kreditur_nama = $penerimaan->kreditur->nama ?? '';

        // Reset details
        $this->details = [];

        foreach ($penerimaan->details as $i => $detail) {
            $obat   = $detail->obat;
            $isi    = $obat->isi_obat ?? 1;
            $utuh   = ($obat && $detail->qty < $isi);

            $this->details[$i] = [
                'id'        => $detail->id, // 🔹 penting untuk update
                'obat_id'   => $obat->id ?? null,
                'nama_obat' => $obat->nama_obat,
                'pabrik_id' => $obat->pabrik_id ?? null,
                'pabrik'    => $obat->pabrik->nama_pabrik ?? '',
                'satuan_id' => $obat->satuan_id ?? null,
                'satuan'    => $obat->satuan->nama_satuan ?? '',
                'sediaan_id' => $obat->sediaan_id ?? null,
                'utuh'      => $utuh,
                'isi_obat'  => $isi,
                'harga'     => $detail->harga,
                'ed'        => $detail->ed ? $detail->ed->format('Y-m-d') : null,
                'batch'     => $detail->batch,
                'qty'       => $detail->qty,
                'disc1'     => $detail->disc1,
                'disc2'     => $detail->disc2,
                'disc3'     => $detail->disc3,
                'subtotal'  => $detail->jumlah,
            ];


            // 🔹 Tambahkan ini supaya input autocomplete tampil
            $this->obatSearch[$i] = $obat->nama_obat ?? '';
            $this->obatResults[$i] = [];
            $this->highlightObatIndex[$i] = 0;
        }

        // Hitung ulang total
        $this->hitungRingkasan();
    }

    public $krediturSearch = [];
    public $showKrediturDropdown = false;
    public $highlightedKrediturIndex = 0;

    public function searchKreditur($value)
    {
        $this->krediturSearch = Kreditur::where('nama', 'like', "%{$value}%")
            ->limit(10)
            ->get();
    }

    public function resetKrediturHighlight()
    {
        $this->highlightedKrediturIndex = 0;
    }

    public function incrementKrediturHighlight()
    {
        if ($this->highlightedKrediturIndex < count($this->krediturSearch) - 1) {
            $this->highlightedKrediturIndex++;
        }
    }

    public function decrementKrediturHighlight()
    {
        if ($this->highlightedKrediturIndex > 0) {
            $this->highlightedKrediturIndex--;
        }
    }

    public function selectHighlightedKreditur()
    {
        $kreditur = $this->krediturSearch[$this->highlightedKrediturIndex] ?? null;
        if ($kreditur) {
            $this->selectKreditur($kreditur->id);
        }
    }

    public function selectKreditur($id)
    {
        $kreditur = Kreditur::find($id);
        if ($kreditur) {
            $this->kreditur_id   = $kreditur->id;
            $this->kreditur_nama = $kreditur->nama;
            $this->showKrediturDropdown = false;
        }
    }

    // 🧩 Properti untuk autocomplete pesanan
    public $no_sp;
    public $pesananSearch = [];
    public $showPesananDropdown = false;
    public $highlightedPesananIndex = 0;

    // 🔍 Fungsi pencarian
    public function searchPesanan($query)
    {
        $this->pesananSearch = \App\Models\Pesanan::query()
            ->where('status', 'pending')
            ->where(function ($q) use ($query) {
                $q->where('no_sp', 'like', "%{$query}%")
                    ->orWhereDate('tanggal', 'like', "%{$query}%");
            })
            ->orderByDesc('tanggal')
            ->limit(10)
            ->get();
    }


    // 🔹 Reset highlight
    public function resetPesananHighlight()
    {
        $this->highlightedPesananIndex = 0;
    }

    // ⬇ Navigasi keyboard
    public function incrementPesananHighlight()
    {
        if ($this->highlightedPesananIndex < count($this->pesananSearch) - 1) {
            $this->highlightedPesananIndex++;
        }
    }

    public function decrementPesananHighlight()
    {
        if ($this->highlightedPesananIndex > 0) {
            $this->highlightedPesananIndex--;
        }
    }

    // ✅ Pilih pesanan
    public function selectHighlightedPesanan()
    {
        if (!empty($this->pesananSearch)) {
            $selected = $this->pesananSearch[$this->highlightedPesananIndex];
            $this->selectPesanan($selected->id);
        }
    }

    public function selectPesanan($id)
    {
        $pesanan = \App\Models\Pesanan::where('id', $id)
            ->where('status', 'pending')
            ->first();


        if (!$pesanan) return;

        $this->pesanan_id = $pesanan->id;
        $this->no_sp = $pesanan->no_sp . ' - ' . $pesanan->tanggal;

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


                // relasi
                $this->details[$i]['pabrik_id']  = $obat->pabrik_id;
                $this->details[$i]['pabrik']     = $obat->pabrik->nama_pabrik ?? '';

                $this->details[$i]['satuan_id']  = $obat->satuan_id;
                $this->details[$i]['satuan']     = $obat->satuan->nama_satuan ?? '';

                $this->details[$i]['sediaan_id'] = $obat->sediaan_id;
                $this->details[$i]['sediaan']    = $obat->sediaan->nama_sediaan ?? '';

                // default utuh true kalau isi_obat > 1
                $this->details[$i]['utuh'] = ($obat->isi_obat ?? 1) > 1 ? true : false;
                $this->details[$i]['isi_obat']   = $obat->isi_obat ?? 1;
                $this->details[$i]['ed'] = Carbon::now()->format('Y-m-d');


                // 🔹 ambil kreditur dari detail pertama jika ada
                $firstDetail = $pesanan->details->first();
                if ($firstDetail) {
                    $this->kreditur_id   = $firstDetail->kreditur_id;
                    $this->kreditur_nama = $firstDetail->kreditur->nama ?? '';
                } else {
                    $this->kreditur_id   = null;
                    $this->kreditur_nama = '';
                }

                // reset search box untuk row ini
                $this->obatSearch[$i]        = $obat->nama_obat;
                $this->obatResults[$i]       = [];
                $this->highlightObatIndex[$i] = 0;

                $this->hitungRingkasan();
            }
        }

        // sembunyikan list pencarian
        $this->pesananList = [];
        $this->highlightIndex = 0;
    }
}
