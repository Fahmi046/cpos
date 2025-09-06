<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Obat;
use Carbon\Carbon;

class PesananForm extends Component
{
    public $pesanan_id;
    public $no_sp;
    public $tanggal;
    public $kategori = '';
    public $kategoriList = [
        'UMUM' => 'UMUM',
        'RS' => 'RS',
        'GROSIR' => 'GROSIR'
    ];
    public $details = [];
    protected $branchCode = 'APT-SHBT';

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->kategori = 'UMUM'; // Default kategori
        $this->details = [
            ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0, 'satuan' => '', 'utuh_satuan' => false]
        ];
        $this->obatList = Obat::all();
        $this->no_sp = $this->generateNoSp();
    }

    private $kategoriCodes = [
        'OOT' => 'SPOT',
        'PREK' => 'SPPR',
        'PSIKO' => 'SPP',
        'NARKO' => 'SPN',
    ];

    public function generateNoSp()
    {
        $tanggal = Carbon::parse($this->tanggal);
        $year = $tanggal->format('Y');
        $month = $this->monthToRoman((int)$tanggal->format('n'));

        // Ambil kode kategori dari mapping
        $kategoriCode = $this->kategoriCodes[$this->kategori] ?? $this->kategori;

        // Hitung urutan SP tahun ini
        $count = Pesanan::whereYear('tanggal', $year)->count() + 1;
        $seq = str_pad($count, 4, '0', STR_PAD_LEFT);

        // Format SP
        return "SP-{$seq}/{$this->branchCode}/{$kategoriCode}/{$month}/{$year}";
    }


    private function monthToRoman($month)
    {
        $map = [
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
            12 => 'XII'
        ];
        return $map[$month] ?? '';
    }

    public function updatedTanggal()
    {
        $this->no_sp = $this->generateNoSp();
    }

    public function updatedKategori($value)
    {
        $this->no_sp = $this->generateNoSp();
    }

    public function addDetail()
    {
        $this->details[] = ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0, 'isi' => 1, 'satuan' => '', 'utuh_satuan' => false];
        $lastIndex = count($this->details) - 1;

        $this->dispatch('focus-row', index: $lastIndex);
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }

    public function updatedDetails()
    {
        foreach ($this->details as $i => $detail) {
            if (empty($detail['obat_id'])) continue;

            $obat = Obat::find($detail['obat_id']);
            if (!$obat) continue;

            $this->details[$i]['harga'] = $obat->harga_jual ?? 0;

            if (!empty($obat->isi_obat) && $obat->isi_obat > 1) {
                $this->details[$i]['utuh_satuan'] = $detail['utuh_satuan'] ?? true;

                if ($this->details[$i]['utuh_satuan']) {
                    // ✅ Kalau centang utuh
                    $this->details[$i]['isi'] = $obat->isi_obat;
                    $this->details[$i]['qty'] = $obat->isi_obat;
                    $this->details[$i]['satuan'] = $obat->satuan->nama_satuan ?? '';
                } else {
                    // ✅ Kalau uncheck
                    $this->details[$i]['isi'] = 1;
                    $this->details[$i]['qty'] = 1;
                    $this->details[$i]['satuan'] = 'PCS';
                }
            } else {
                $this->details[$i]['utuh_satuan'] = false;
                $this->details[$i]['isi'] = 1;
                $this->details[$i]['qty'] = $detail['qty'] ?? 1;
                $this->details[$i]['satuan'] = 'PCS';
            }

            // Hitung jumlah total
            $this->details[$i]['jumlah'] =
                ($this->details[$i]['qty'] ?? 1) * ($this->details[$i]['harga'] ?? 0);
        }
    }




    public function save()
    {
        $this->validate([
            'no_sp' => 'required|unique:pesanan,no_sp,' . $this->pesanan_id,
            'tanggal' => 'required|date',
            'details.*.obat_id' => 'required|exists:obat,id',
            'details.*.qty' => 'required|numeric|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        if ($this->pesanan_id) {
            // Update
            $pesanan = Pesanan::findOrFail($this->pesanan_id);
            $pesanan->update([
                'no_sp'    => $this->no_sp,
                'tanggal'  => $this->tanggal,
                'kategori' => $this->kategori,
            ]);

            $pesanan->details()->delete();
            foreach ($this->details as $detail) {
                $pesanan->details()->create($detail);
            }

            session()->flash('message', 'Pesanan berhasil diperbarui!');
        } else {
            // Insert baru
            $pesanan = Pesanan::create([
                'no_sp'    => $this->no_sp,
                'tanggal'  => $this->tanggal,
                'kategori' => $this->kategori,
            ]);

            foreach ($this->details as $detail) {
                $pesanan->details()->create($detail);
            }

            session()->flash('message', 'Pesanan berhasil disimpan!');
        }

        $this->resetForm();
        $this->dispatch('refreshTable');
        $this->dispatch('focus-tanggal');
    }


    private function resetForm()
    {
        $this->pesanan_id = null;
        $this->tanggal = date('Y-m-d');
        $this->kategori = 'UMUM';
        $this->details = [
            ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0, 'satuan' => '', 'utuh_satuan' => false]
        ];
        $this->no_sp = $this->generateNoSp();
    }


    public function render()
    {
        return view('livewire.pesanan-form');
    }

    protected $listeners = [
        'refreshKodepesanan' => 'refreshKodepesanan',
        'edit-pesanan' => 'edit',
    ];
    public function edit($id)
    {
        $pesanan = Pesanan::with('details.obat')->findOrFail($id);

        $this->pesanan_id = $pesanan->id;
        $this->no_sp = $pesanan->no_sp;
        $this->tanggal = $pesanan->tanggal;
        $this->kategori = $pesanan->kategori;

        $this->details = [];

        foreach ($pesanan->details as $detail) {
            $obat = $detail->obat;

            $utuh = false;
            $qty = $detail->qty;
            $satuan = 'PCS';

            if ($obat && !empty($obat->isi_obat) && $obat->isi_obat > 1) {
                if ($qty == $obat->isi_obat) {
                    $utuh = true;
                    $satuan = $obat->satuan->nama_satuan ?? '';
                } else {
                    $utuh = false;
                    $satuan = 'PCS';
                }
            }

            $this->details[] = [
                'obat_id'      => $obat->id,
                'nama_obat'    => $obat->nama_obat,
                'qty'          => $qty,
                'harga'        => $detail->harga,
                'jumlah'       => $detail->jumlah,
                'satuan'       => $satuan,
                'utuh_satuan'  => $utuh,
            ];
        }
    }

    public function refreshKodepesanan()
    {
        $this->no_sp = $this->generateNoSp();
    }
    public $obatList = [];
    public $obatSearch = [];          // Hasil pencarian per index row
    public $showObatDropdown = [];    // Status dropdown per index row

    public function searchObat($index, $query)
    {
        $this->obatSearch[$index] = Obat::where('nama_obat', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        $this->showObatDropdown[$index] = true;
    }

    public function selectObat($index, $obatId)
    {
        $obat = Obat::find($obatId);
        if (!$obat) return;

        $this->details[$index]['obat_id'] = $obat->id;
        $this->details[$index]['nama_obat'] = $obat->nama_obat ?? '';
        $this->details[$index]['harga'] = $obat->harga_jual ?? 0;
        $this->details[$index]['satuan'] = $obat->satuan->nama_satuan ?? '';

        // cek apakah isi_obat ada
        if (!empty($obat->isi_obat) && $obat->isi_obat > 1) {
            $this->details[$index]['utuh_satuan'] = true;   // auto-checklist
            $this->details[$index]['isi'] = $obat->isi_obat;
        } else {
            $this->details[$index]['utuh_satuan'] = false;  // tidak dicentang
            $this->details[$index]['isi'] = 1;
        }
    }



    public function selectFirstObat($index)
    {
        if (!empty($this->obatSearch[$index])) {
            $firstObat = $this->obatSearch[$index]->first();
            $this->selectObat($index, $firstObat->id);
        }
    }

    public $highlightedIndex = []; // simpan index highlight per row

    public function resetHighlight($index)
    {
        $this->highlightedIndex[$index] = 0;
    }

    public function incrementHighlight($index)
    {
        if (!isset($this->highlightedIndex[$index])) {
            $this->highlightedIndex[$index] = 0;
        }

        $count = isset($this->obatSearch[$index]) ? $this->obatSearch[$index]->count() : 0;

        if ($count > 0) {
            $this->highlightedIndex[$index] = ($this->highlightedIndex[$index] + 1) % $count;
        }
    }

    public function decrementHighlight($index)
    {
        if (!isset($this->highlightedIndex[$index])) {
            $this->highlightedIndex[$index] = 0;
        }

        $count = isset($this->obatSearch[$index]) ? $this->obatSearch[$index]->count() : 0;

        if ($count > 0) {
            $this->highlightedIndex[$index] = ($this->highlightedIndex[$index] - 1 + $count) % $count;
        }
    }

    public function selectHighlightedObat($index)
    {
        if (!isset($this->highlightedIndex[$index])) {
            $this->highlightedIndex[$index] = 0;
        }

        if (!empty($this->obatSearch[$index])) {
            $selected = $this->obatSearch[$index][$this->highlightedIndex[$index]];

            $this->details[$index]['obat_id'] = $selected->id;
            $this->details[$index]['nama_obat'] = $selected->nama_obat;
            $this->details[$index]['harga'] = $selected->harga_jual ?? 0;
            $this->details[$index]['satuan'] = $selected->satuan?->nama_satuan ?? '';

            if (!empty($selected->isi_obat) && $selected->isi_obat > 1) {
                $this->details[$index]['utuh_satuan'] = true; // ✅ auto-checklist

                // 👉 qty otomatis ikut isi_obat hanya saat pertama kali pilih
                $this->details[$index]['qty'] = $selected->isi_obat;
            } else {
                $this->details[$index]['utuh_satuan'] = false;

                // qty default 1 kalau tidak ada isi_obat
                $this->details[$index]['qty'] = 1;
            }

            // hitung jumlah
            $qty = $this->details[$index]['qty'] ?? 1;
            $this->details[$index]['jumlah'] = $qty * ($this->details[$index]['harga'] ?? 0);

            $this->showObatDropdown[$index] = false;
        }
    }



    public function toggleUtuhSatuan($index)
    {
        if (!isset($this->details[$index])) return;

        $detail = $this->details[$index];
        $obat = Obat::find($detail['obat_id'] ?? null);

        if ($obat) {
            if (!empty($detail['utuh_satuan']) && $detail['utuh_satuan']) {
                $this->details[$index]['qty'] = $obat->isi_obat ?? 1;
                $this->details[$index]['satuan'] = $obat->satuan->nama_satuan ?? '';
            } else {
                $this->details[$index]['qty'] = 1;
                $this->details[$index]['satuan'] = 'PCS';
            }

            $this->details[$index]['jumlah'] =
                ($this->details[$index]['qty'] ?? 1) * ($this->details[$index]['harga'] ?? 0);
        }
    }
}
