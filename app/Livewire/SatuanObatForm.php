<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SatuanObat;
use Illuminate\Validation\Rule;

class SatuanObatForm extends Component
{
    public $satuan_id;
    public $kode_satuan, $nama_satuan, $deskripsi, $aktif = true;

    protected function rules()
    {
        return [
            'kode_satuan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('satuan_obat', 'kode_satuan')->ignore($this->satuan_id),
            ],
            'nama_satuan' => 'required|string|max:50',
            'deskripsi'   => 'nullable|string|max:150',
            'aktif'       => 'boolean',
        ];
    }
    protected $listeners = ['edit-satuan' => 'edit', 'resetForm' => 'resetInput'];

    public function mount()
    {
        // generate kode awal saat form pertama kali dibuka
        $this->kode_satuan = $this->generateKode();
    }

    private function generateKode()
    {
        $prefix = '0011';
        $last = SatuanObat::orderBy('id', 'desc')->first();

        if ($last) {
            // ambil 4 digit terakhir
            $lastNumber = (int) substr($last->kode_satuan, -4);
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber; // <-- pakai return
    }


    public function save()
    {
        $this->validate();

        if ($this->satuan_id) {
            // update
            $satuan = SatuanObat::findOrFail($this->satuan_id);
            $satuan->update([
                'kode_satuan' => $this->kode_satuan,
                'nama_satuan' => $this->nama_satuan,
                'deskripsi'   => $this->deskripsi,
                'aktif'       => $this->aktif,
            ]);
            session()->flash('message', 'Data berhasil diperbarui.');
        } else {
            // insert baru
            SatuanObat::create([
                'kode_satuan' => $this->kode_satuan,
                'nama_satuan' => $this->nama_satuan,
                'deskripsi'   => $this->deskripsi,
                'aktif'       => $this->aktif,
            ]);
            session()->flash('message', 'Data berhasil disimpan.');
        }
        $this->resetInput();
        $this->dispatch('focus-nama-satuan');
        $this->dispatch('refreshTable');
    }

    public function edit($id)
    {
        $satuan = SatuanObat::findOrFail($id);

        $this->satuan_id   = $satuan->id;
        $this->kode_satuan = $satuan->kode_satuan;
        $this->nama_satuan = $satuan->nama_satuan;
        $this->deskripsi   = $satuan->deskripsi;
        $this->aktif       = $satuan->aktif;
        $this->dispatch('focus-nama-satuan');
    }


    public function resetInput()
    {
        $this->satuan_id   = null;
        $this->kode_satuan = $this->generateKode();
        $this->nama_satuan = '';
        $this->deskripsi   = '';
        $this->aktif       = true;

        $this->dispatch('focus-nama-satuan');
    }


    public function render()
    {
        return view('livewire.satuan-obat-form');
    }
}
