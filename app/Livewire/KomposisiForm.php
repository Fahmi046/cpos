<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Komposisi;
use Illuminate\Validation\Rule;

class KomposisiForm extends Component
{
    public $komposisi_id;
    public $kode_komposisi;
    public $nama_komposisi;
    public $deskripsi;

    protected function rules()
    {
        return [
            'kode_komposisi' => [
                'required',
                Rule::unique('komposisi', 'kode_komposisi')->ignore($this->komposisi_id)
            ],
            'nama_komposisi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ];
    }

    public function store()
    {
        $this->validate();

        komposisi::updateOrCreate(
            ['id' => $this->komposisi_id],
            [
                'kode_komposisi' => $this->kode_komposisi,
                'nama_komposisi' => $this->nama_komposisi,
                'deskripsi' => $this->deskripsi,
            ]
        );

        $this->dispatch('komposisi-store');
        $this->resetForm();
        $this->generateKodeKomposisi(); // ✅ setelah reset, buat lagi kode baru untuk input berikutnya
        $this->dispatch('focus-nama-komposisi');
        $this->dispatch('refreshTable');
    }

    public function edit($id)
    {
        $komposisi = Komposisi::findOrFail($id);
        $this->fill($komposisi->toArray());
        $this->komposisi_id = $id;
    }

    public function render()
    {
        return view('livewire.komposisi-form');
    }


    public function mount($komposisi_id = null)
    {
        $this->generateKodeKomposisi();
    }
    public function generateKodeKomposisi()
    {
        $last = komposisi::orderBy('id', 'desc')->first();

        if ($last && $last->kode_komposisi) {
            // Ambil hanya angka setelah prefix "0010"
            $lastNumber = intval(substr($last->kode_komposisi, 4));
            $nextNumber = $lastNumber + 1;
        } else {
            // Kalau belum ada data → mulai dari 1
            $nextNumber = 1;
        }

        // Format hasilnya
        $this->kode_komposisi = '0014' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    protected $listeners = [
        'edit-komposisi'   => 'edit',
        'refreshForm' => '$refresh',
        'refreshKodeKomposisi' => 'generateKodeKomposisi',
    ];

    public function resetForm(): void
    {
        $this->reset([
            'komposisi_id',
            'kode_komposisi',
            'nama_komposisi',
            'deskripsi'
        ]);

        $this->kode_komposisi = $this->generateKodeKomposisi();
    }
}
