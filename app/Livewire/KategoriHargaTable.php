<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KategoriHarga;

class KategoriHargaTable extends Component
{
    use WithPagination;

    protected $listeners = ['kategoriDisimpan' => '$refresh'];

    // Agar pagination tetap di halaman 1 setelah simpan
    protected $updatesQueryString = ['page'];

    public function render()
    {
        $kategoris = KategoriHarga::orderBy('nama')->paginate(10);

        return view('livewire.kategori-harga-table', [
            'kategoris' => $kategoris,
        ]);
    }

    // 🔸 Kirim event edit ke komponen form
    public function edit($id)
    {
        $this->dispatch('editKategori', id: $id);
    }

    // 🔸 Hapus data dengan notifikasi
    public function delete($id)
    {
        $kategori = KategoriHarga::find($id);

        if ($kategori) {
            $kategori->delete();
            $this->dispatch('toast', message: 'Kategori berhasil dihapus.');
        } else {
            $this->dispatch('toast', message: 'Kategori tidak ditemukan.');
        }
    }
}
