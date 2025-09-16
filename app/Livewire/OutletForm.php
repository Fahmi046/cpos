<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Outlet;

class OutletForm extends Component
{
    public $kode_outlet, $nama_outlet, $alamat, $telepon, $pic, $aktif = 1, $outlet_id;
    public $isEdit = false;

    protected $listeners = [
        'editOutlet' => 'edit'
    ];

    public function render()
    {
        return view('livewire.outlet-form');
    }

    public function resetInput()
    {
        $this->kode_outlet = '';
        $this->nama_outlet = '';
        $this->alamat = '';
        $this->telepon = '';
        $this->pic = '';
        $this->aktif = 1;
        $this->outlet_id = null;
        $this->isEdit = false;
    }

    public function store()
    {
        $this->validate([
            'kode_outlet' => 'required|unique:outlets,kode_outlet',
            'nama_outlet' => 'required|string|max:255',
        ]);

        Outlet::create([
            'kode_outlet' => $this->kode_outlet,
            'nama_outlet' => $this->nama_outlet,
            'alamat' => $this->alamat,
            'telepon' => $this->telepon,
            'pic' => $this->pic,
            'aktif' => $this->aktif,
        ]);

        session()->flash('message', 'Outlet berhasil ditambahkan');
        $this->resetInput();

        $this->emit('refreshOutletTable');
    }

    public function edit($id)
    {
        $outlet = Outlet::findOrFail($id);
        $this->outlet_id = $id;
        $this->kode_outlet = $outlet->kode_outlet;
        $this->nama_outlet = $outlet->nama_outlet;
        $this->alamat = $outlet->alamat;
        $this->telepon = $outlet->telepon;
        $this->pic = $outlet->pic;
        $this->aktif = $outlet->aktif;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate([
            'kode_outlet' => 'required|unique:outlets,kode_outlet,' . $this->outlet_id,
            'nama_outlet' => 'required|string|max:255',
        ]);

        $outlet = Outlet::findOrFail($this->outlet_id);
        $outlet->update([
            'kode_outlet' => $this->kode_outlet,
            'nama_outlet' => $this->nama_outlet,
            'alamat' => $this->alamat,
            'telepon' => $this->telepon,
            'pic' => $this->pic,
            'aktif' => $this->aktif,
        ]);

        session()->flash('message', 'Outlet berhasil diperbarui');
        $this->resetInput();

        $this->emit('refreshOutletTable');
    }
}
