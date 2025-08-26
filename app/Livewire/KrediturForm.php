<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kreditur;
use Illuminate\Validation\Rule;

class KrediturForm extends Component
{
    public $kreditur_id;
    public $kode_kreditur, $nama, $alamat, $telepon, $email, $aktif = true;

    protected $listeners = [
        'openForm' => 'create',
        'edit-kreditur' => 'edit',
        'refreshKodeKredit' => 'generateKodeKredit',
    ];

    protected function rules()
    {
        return [
            'kode_kreditur' => [
                'required',
                Rule::unique('kreditur', 'kode_kreditur')->ignore($this->kreditur_id)
            ],
            'nama' => 'required|string|max:150',
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'aktif' => 'boolean',
        ];
    }

    public function render()
    {
        return view('livewire.kreditur-form');
    }

    public function edit($id)
    {
        $kreditur = Kreditur::findOrFail($id);
        $this->fill($kreditur->toArray());
        $this->kreditur_id = $id;
    }

    public function save()
    {
        $this->validate();

        Kreditur::updateOrCreate(
            ['id' => $this->kreditur_id],
            [
                'kode_kreditur' => $this->kode_kreditur,
                'nama' => $this->nama,
                'alamat' => $this->alamat,
                'telepon' => $this->telepon,
                'email' => $this->email,
                'aktif' => $this->aktif,
            ]
        );

        $this->dispatch('kreditur-saved');
        $this->resetForm();
        $this->generateKodeKreditur(); // ✅ setelah reset, buat lagi kode baru untuk input berikutnya
        $this->dispatch('focus-nama');
        $this->dispatch('refreshTable');
    }

    private function resetForm()
    {
        $this->reset(['kreditur_id', 'nama', 'alamat', 'telepon', 'email']);
        $this->aktif = true;
        $this->kode_kreditur = $this->generateKodeKreditur();
    }

    public function mount($kreditur_id = null)
    {
        $this->generateKodeKreditur();
    }
    public function generateKodeKreditur()
    {
        $last = Kreditur::orderBy('id', 'desc')->first();

        if ($last && $last->kode_kreditur) {
            // Ambil hanya angka setelah prefix "0010"
            $lastNumber = intval(substr($last->kode_kreditur, 4));
            $nextNumber = $lastNumber + 1;
        } else {
            // Kalau belum ada data → mulai dari 1
            $nextNumber = 1;
        }

        // Format hasilnya
        $this->kode_kreditur = '0015' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
