<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Obat;

class PesananForm extends Component
{
    public $no_sp;
    public $tanggal;
    public $details = [];
    public $obatList;

    public function mount()
    {
        $this->tanggal = date('Y-m-d'); // Default hari ini
        $this->details = [
            ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0]
        ];
        $this->obatList = Obat::all();
    }

    public function addDetail()
    {
        $this->details[] = ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0];
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details); // Reindex array
    }

    public function updatedDetails()
    {
        foreach ($this->details as $i => $detail) {
            $qty = $detail['qty'] ?? 0;
            $harga = $detail['harga'] ?? 0;
            $this->details[$i]['jumlah'] = $qty * $harga;
        }
    }

    public function save()
    {
        $this->validate([
            'no_sp' => 'required',
            'tanggal' => 'required|date',
            'details.*.obat_id' => 'required|exists:obat,id',
            'details.*.qty' => 'required|numeric|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

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
        $this->no_sp = '';
        $this->tanggal = date('Y-m-d');
        $this->details = [
            ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0]
        ];
    }

    public function render()
    {
        return view('livewire.pesanan-form');
    }
}
