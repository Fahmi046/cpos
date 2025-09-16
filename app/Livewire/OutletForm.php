<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Outlet;

class OutletForm extends Component
{
    public $outlet_id;
    public $kode_outlet;
    public $nama_outlet;
    public $alamat;
    public $telepon;
    public $pic;
    public $aktif = true;

    protected $rules = [
        'kode_outlet' => 'required|string|max:50',
        'nama_outlet' => 'required|string|max:100',
        'alamat'      => 'nullable|string|max:255',
        'telepon'     => 'nullable|string|max:20',
        'aktif'       => 'boolean',
    ];

    protected $listeners = [
        'editOutlet' => 'edit',
        'refreshForm' => '$refresh',
        'refreshKodeoutlet' => 'generateKodeOutlet',
    ];

    public function render()
    {
        return view('livewire.outlet-form');
    }

    public function save()
    {
        $this->validate();

        Outlet::updateOrCreate(
            ['id' => $this->outlet_id],
            [
                'kode_outlet' => $this->kode_outlet,
                'nama_outlet' => $this->nama_outlet,
                'alamat'      => $this->alamat,
                'telepon'     => $this->telepon,
                'pic'     => $this->pic,
                'aktif'       => $this->aktif,
            ]
        );

        $this->dispatch('outlet-saved');
        $this->resetForm();
        $this->generateKodeOutlet();
        $this->dispatch('focus-nama-outlet');
        $this->dispatch('refreshOutletTable');
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
    }

    public function mount($outlet_id = null)
    {
        $this->generateKodeOutlet();
    }
    public function generateKodeOutlet()
    {
        $last = Outlet::orderBy('id', 'desc')->first();

        if ($last && $last->kode_outlet) {
            // Ambil hanya angka setelah prefix "0010"
            $lastNumber = intval(substr($last->kode_outlet, 4));
            $nextNumber = $lastNumber + 1;
        } else {
            // Kalau belum ada data → mulai dari 1
            $nextNumber = 1;
        }

        // Format hasilnya
        $this->kode_outlet = '0020' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function resetForm(): void
    {
        $this->reset([
            'outlet_id',
            'kode_outlet',
            'nama_outlet',
            'alamat',
            'telepon',
            'pic',
            'aktif'
        ]);

        $this->kode_outlet = $this->generateKodeOutlet();
    }
}
