<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BentukSediaan;
use Illuminate\Validation\Rule;

class BentukSediaanForm extends Component
{
    protected function rules()
    {
        return [
            'kode_sediaan' => [
                'required',
                Rule::unique('bentuk_sediaans', 'kode_sediaan')->ignore($this->sediaan_id),
            ],
            'nama_sediaan' => 'required|string|max:100',
            'deskripsi'   => 'nullable|string',
            'aktif'       => 'boolean',
        ];
    }

    public $sediaan_id;
    public $kode_sediaan, $nama_sediaan, $deskripsi, $aktif = true;


    public function mount($sediaan_id = null)
    {
        $this->generateKodeSediaan();
    }
    public function generateKodeSediaan()
    {
        $last = BentukSediaan::orderBy('id', 'desc')->first();

        if ($last && $last->kode_sediaan) {
            // Ambil hanya angka setelah prefix "0010"
            $lastNumber = intval(substr($last->kode_sediaan, 4));
            $nextNumber = $lastNumber + 1;
        } else {
            // Kalau belum ada data → mulai dari 1
            $nextNumber = 1;
        }

        // Format hasilnya
        $this->kode_sediaan = '0012' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    protected $listeners = [
        'edit-sediaan'   => 'edit',
        'refreshForm' => '$refresh',
        'refreshKodeSediaan' => 'generateKodeSediaan',
    ];
    public function save()
    {
        $this->validate();

        // ✅ pastikan kode_sediaan di-generate sebelum simpan
        if (!$this->sediaan_id && !$this->kode_sediaan) {
            $this->generateKodesediaan();
        }

        BentukSediaan::updateOrCreate(
            ['id' => $this->sediaan_id],
            [
                'kode_sediaan' => $this->kode_sediaan,
                'nama_sediaan' => $this->nama_sediaan,
                'deskripsi'    => $this->deskripsi,
                'aktif'        => $this->aktif,
            ]
        );

        $this->dispatch('sediaan-saved');
        $this->resetForm();
        $this->generateKodeSediaan(); // ✅ setelah reset, buat lagi kode baru untuk input berikutnya
        $this->dispatch('focus-nama-sediaan');  // 🔥 trigger event fokus
        $this->dispatch('refreshTable');
    }

    public function render()
    {
        return view('livewire.bentuk-sediaan-form');
    }


    public function edit($id)
    {
        $sediaan = BentukSediaan::findOrFail($id);
        $this->fill($sediaan->toArray());
        $this->sediaan_id = $id;
    }

    public function resetForm(): void
    {
        $this->reset([
            'sediaan_id',
            'kode_sediaan',
            'nama_sediaan',
            'deskripsi',
            'aktif'
        ]);

        $this->kode_obat = $this->generateKodeSediaan();
    }
}
