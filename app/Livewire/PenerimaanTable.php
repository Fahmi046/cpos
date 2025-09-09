<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Penerimaan;

class PenerimaanTable extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $penerimaan = Penerimaan::with('pesanan', 'kreditur')
            ->where('no_penerimaan', 'like', "%{$this->search}%")
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('livewire.penerimaan-table', compact('penerimaan'));
    }
}
