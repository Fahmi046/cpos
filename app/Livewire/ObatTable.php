<?php

namespace App\Livewire;

use App\Models\Obat;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ObatExport;
use App\Imports\ObatImport;
use App\Exports\ObatTemplateExport;

class ObatTable extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    public $search = '';

    public $file; // untuk upload file
    protected $updatesQueryString = ['search'];

    protected $listeners = ['refreshTable' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Obat::findOrFail($id)->delete();
        session()->flash('message', 'Obat berhasil dihapus.');
        $this->dispatch('refreshKodeObat');
        $this->dispatch('focus-nama-obat');
    }

    public function render()
    {
        $obats = Obat::with(['kategori', 'sediaan', 'komposisi', 'satuan', 'pabrik'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('kode_obat', 'like', '%' . $this->search . '%')
                        ->orWhere('nama_obat', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(4);


        return view('livewire.obat-table', [
            'obats' => $obats,
        ]);
    }



    public function exportExcel()
    {
        return Excel::download(new ObatExport($this->search), 'master_obat.xlsx');
    }

    public function importExcel()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ObatImport, $this->file->getRealPath());

        session()->flash('message', 'Data Obat berhasil diupload!');
    }

    public function downloadTemplate()
    {
        return Excel::download(new ObatTemplateExport, 'template_obat.xlsx');
    }
}
