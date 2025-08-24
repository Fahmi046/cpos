<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KategoriObat;
use Illuminate\Validation\Rule;

class KategoriObatForm extends Component
{
    public $kategori_id;
    public $kode_kategori, $nama_kategori, $deskripsi, $aktif = true;

    protected function rules()
    {
        return [
            'kode_kategori' => [
                'required',
                Rule::unique('kategori_obat', 'kode_kategori')->ignore($this->kategori_id),
            ],
            'nama_kategori' => 'required|string',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        KategoriObat::updateOrCreate(
            ['id' => $this->kategori_id],
            [
                'kode_kategori' => $this->kode_kategori,
                'nama_kategori' => $this->nama_kategori,
                'deskripsi' => $this->deskripsi,
                'aktif' => $this->aktif,
            ]
        );

        $this->dispatch('kategori-saved');
        $this->resetForm();
        $this->generateKodeKategori();
        $this->dispatch('focus-nama-kategori');
        $this->dispatch('kategori-updated'); // refresh tabel
    }

    public function edit($id)
    {
        $kategori = KategoriObat::findOrFail($id);
        $this->fill($kategori->toArray());
        $this->kategori_id = $id;
    }

    public function resetForm()
    {
        $this->reset(['kategori_id', 'kode_kategori', 'nama_kategori', 'deskripsi', 'aktif']);
        $this->aktif = true;

        $this->kode_kategori = $this->generateKodeKategori();
    }

    public function render()
    {
        return view('livewire.kategori-obat-form');
    }

    public function mount($kategori_id = null)
    {
        $this->generateKodekategori();
    }
    public function generateKodekategori()
    {
        $last = KategoriObat::orderBy('id', 'desc')->first();

        if ($last && $last->kode_kategori) {
            // Ambil hanya angka setelah prefix "0010"
            $lastNumber = intval(substr($last->kode_kategori, 4));
            $nextNumber = $lastNumber + 1;
        } else {
            // Kalau belum ada data → mulai dari 1
            $nextNumber = 1;
        }

        // Format hasilnya
        $this->kode_kategori = '0014' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    protected $listeners = [
        'edit-kategori'   => 'edit',
        'refreshForm' => '$refresh',
        'refreshKodeKategori' => 'generateKodeKategori',
    ];
}
