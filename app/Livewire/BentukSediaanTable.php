<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BentukSediaan;

class BentukSediaanTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['refreshTable' => '$refresh'];

    public function delete($id)
    {
        BentukSediaan::findOrFail($id)->delete();

        session()->flash('message', 'Sediaan berhasil dihapus.');

        $this->dispatch('refreshKodeSediaan');
        $this->dispatch('focus-nama-sediaan');

        // otomatis refresh karena pakai $refresh listener
        $this->resetPage(); // biar pagination tidak error kalau data terakhir dihapus
    }

    public function render()
    {
        return view('livewire.bentuk-sediaan-table', [
            'sediaans' => BentukSediaan::orderBy('nama_sediaan')->paginate(10),
        ]);
    }
}
