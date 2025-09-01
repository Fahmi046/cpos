<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Obat;
use Carbon\Carbon;

class PesananForm extends Component
{
    public $no_sp;
    public $tanggal;
    public $kategori = '';
    public $kategoriList = [
        'UMUM' => 'UMUM',
        'RS' => 'RS',
        'GROSIR' => 'GROSIR'
    ];
    public $details = [];
    public $obatList;

    protected $branchCode = 'APT-SHBT';

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->kategori = 'UMUM'; // Default kategori
        $this->details = [
            ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0]
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

    private function generateNoSp()
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
        $this->details[] = ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0];
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }

    public function updatedDetails($value, $name)
    {
        foreach ($this->details as $i => $detail) {

            // Jika obat_id berubah → ambil data obat dari DB
            if (!empty($detail['obat_id'])) {
                $obat = Obat::find($detail['obat_id']);
                if ($obat) {
                    $this->details[$i]['harga'] = $obat->harga_jual;
                    $this->details[$i]['isi'] = $obat->isi_obat ?? 1;
                    $this->details[$i]['satuan'] = $obat->satuan->nama_satuan ?? '';
                }
            }

            // Hitung jumlah otomatis saat qty atau harga berubah
            $qty = $detail['qty'] ?? 0;
            $harga = $detail['harga'] ?? 0;
            $isi = $detail['isi'] ?? 1;

            $this->details[$i]['jumlah'] = $qty * $harga * $isi;
        }
    }



    public function save()
    {
        $this->validate([
            'no_sp' => 'required|unique:pesanan,no_sp',
            'tanggal' => 'required|date',
            'details.*.obat_id' => 'required|exists:obat,id',
            'details.*.qty' => 'required|numeric|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        $this->no_sp = $this->generateNoSp();

        $pesanan = Pesanan::create([
            'no_sp' => $this->no_sp,
            'tanggal' => $this->tanggal,
        ]);

        foreach ($this->details as $detail) {
            PesananDetail::create([
                'pesanan_id' => $pesanan->id,
                'obat_id' => $detail['obat_id'],
                'qty' => $detail['qty'],
                'harga' => $detail['harga'],
                'jumlah' => $detail['jumlah'],
            ]);
        }

        session()->flash('message', 'Pesanan berhasil disimpan!');
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->tanggal = date('Y-m-d');
        $this->kategori = 'UMUM';
        $this->details = [
            ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0]
        ];
        $this->no_sp = $this->generateNoSp();
    }

    public function render()
    {
        return view('livewire.pesanan-form');
    }
}
