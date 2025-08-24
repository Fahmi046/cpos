<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pabrik;
use Illuminate\Validation\Rule;

class PabrikForm extends Component
{
    public $pabrik_id;
    public $kode_pabrik, $nama_pabrik, $alamat, $telepon, $aktif = true;

    protected function rules()
    {
        return [
            'kode_pabrik' => [
                'required',
                Rule::unique('pabrik', 'kode_pabrik')->ignore($this->pabrik_id)
            ],
            'nama_pabrik' => 'required|string',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string',
            'aktif' => 'boolean'
        ];
    }

    public function save()
    {
        $this->validate();

        Pabrik::updateOrCreate(
            ['id' => $this->pabrik_id],
            [
                'kode_pabrik' => $this->kode_pabrik,
                'nama_pabrik' => $this->nama_pabrik,
                'alamat' => $this->alamat,
                'telepon' => $this->telepon,
                'aktif' => $this->aktif,
            ]
        );

        $this->dispatch('pabrik-saved');
        $this->resetForm();
        $this->generateKodePabrik(); // ✅ setelah reset, buat lagi kode baru untuk input berikutnya
        $this->dispatch('focus-nama-pabrik');
        $this->dispatch('refreshTable');
    }

    public function edit($id)
    {
        $pabrik = Pabrik::findOrFail($id);
        $this->pabrik_id = $pabrik->id;
        $this->kode_pabrik = $pabrik->kode_pabrik;
        $this->nama_pabrik = $pabrik->nama_pabrik;
        $this->alamat = $pabrik->alamat;
        $this->telepon = $pabrik->telepon;
        $this->aktif = $pabrik->aktif;
    }

    public function render()
    {
        return view('livewire.pabrik-form');
    }

    public function mount($pabrik_id = null)
    {
        $this->generateKodePabrik();
    }
    public function generateKodePabrik()
    {
        $last = Pabrik::orderBy('id', 'desc')->first();

        if ($last && $last->kode_pabrik) {
            // Ambil hanya angka setelah prefix "0010"
            $lastNumber = intval(substr($last->kode_pabrik, 4));
            $nextNumber = $lastNumber + 1;
        } else {
            // Kalau belum ada data → mulai dari 1
            $nextNumber = 1;
        }

        // Format hasilnya
        $this->kode_pabrik = '0013' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    protected $listeners = [
        'edit-pabrik'   => 'edit',
        'refreshForm' => '$refresh',
        'refreshKodePabrik' => 'generateKodePabrik',
    ];

    public function resetForm(): void
    {
        $this->reset([
            'pabrik_id',
            'kode_pabrik',
            'nama_pabrik',
            'alamat',
            'telepon',
            'aktif'
        ]);

        $this->kode_pabrik = $this->generateKodePabrik();
    }
}
