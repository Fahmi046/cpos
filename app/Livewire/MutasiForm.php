<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Mutasi;
use App\Models\MutasiDetail;
use App\Models\Obat;

class MutasiForm extends Component
{
    public $mutasi_id;
    public $no_mutasi;
    public $tanggal;
    public $outlet_id;
    public $keterangan;

    public $detail = []; // array detail mutasi
    public $obat_id;
    public $qty = 1;
    public $batch;
    public $ed;

    public $outlets = [];
    public $obats = [];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->no_mutasi = 'MTS-' . now()->format('Ymd-His');
        $this->detail = [];
        $this->outlets = \App\Models\Outlet::all();
        $this->obats = Obat::all();
    }

    public function addDetail()
    {
        if (empty($this->obat_id)) return;

        $obat = Obat::find($this->obat_id);
        if (!$obat) return;

        $this->detail[] = [
            'obat_id'    => $obat->id,
            'nama_obat'  => $obat->nama_obat,
            'qty'        => $this->qty ?? 1,
            'batch'      => $this->batch,
            'ed'         => $this->ed,
            'satuan_id'  => $obat->satuan_id ?? 1,
            'sediaan_id' => $obat->sediaan_id ?? 1,
            'pabrik_id'  => $obat->pabrik_id ?? 1,
            'utuhan'     => $obat->utuhan ?? 0,
        ];

        // reset input
        $this->obat_id = null;
        $this->qty = 1;
        $this->batch = '';
        $this->ed = '';
    }

    public function removeDetail($index)
    {
        unset($this->detail[$index]);
        $this->detail = array_values($this->detail);
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
        $this->detail = [];
        $this->obat_id = null;
        $this->qty = 1;
        $this->batch = '';
        $this->ed = '';
    }


    public function render()
    {
        return view('livewire.mutasi-form');
    }
}
