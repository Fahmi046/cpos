<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Mutasi;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\MutasiExport;
use Maatwebsite\Excel\Facades\Excel;

class MutasiTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshTable' => 'loadData'];

    public $search = '';
    public $selectedId;
    public $no_mutasi, $tanggal, $outlet_id, $keterangan = '';
    public $details = [];

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $this->selectedId = $id;
        $mutasi = Mutasi::with('details.obat')->findOrFail($id);

        $this->no_mutasi  = $mutasi->no_mutasi;
        $this->tanggal    = $mutasi->tanggal;
        $this->outlet_id  = $mutasi->outlet_id;
        $this->keterangan = $mutasi->keterangan;

        $this->details = $mutasi->details->map(function ($detail) {
            return [
                'obat_id'    => $detail->obat_id,
                'nama_obat'  => $detail->obat->nama_obat ?? '',
                'qty'        => $detail->qty,
                'harga'      => $detail->harga,
                'jumlah'     => $detail->jumlah,
                'ed'         => $detail->ed,
                'batch'      => $detail->batch,
                'satuan_id'  => $detail->satuan_id,
                'sediaan_id' => $detail->sediaan_id,
                'pabrik_id'  => $detail->pabrik_id,
                'utuh'       => $detail->utuh,
            ];
        })->toArray();

        $this->dispatch('focus-tanggal');
    }

    public function delete($id)
    {
        $mutasi = Mutasi::findOrFail($id);
        $mutasi->details()->delete();
        $mutasi->delete();
        $this->loadData();
        session()->flash('message', 'Mutasi berhasil dihapus.');

        $this->dispatch('refreshKodeMutasi');
        $this->dispatch('focus-tanggal');
    }

    public function exportExcel()
    {
        $tanggal = Carbon::today()->format('Y-m-d');
        $fileName = "mutasi_{$tanggal}.xlsx";

        return Excel::download(new MutasiExport($this->search), $fileName);
    }

    public function render()
    {
        $mutasiList = Mutasi::with(['details.obat', 'outlet'])
            ->where(function ($query) {
                $query->where('no_mutasi', 'like', '%' . $this->search . '%')
                    ->orWhere('tanggal', 'like', '%' . $this->search . '%')
                    ->orWhereHas('details.obat', function ($q) {
                        $q->where('nama_obat', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(5);

        return view('livewire.mutasi-table', [
            'mutasiList' => $mutasiList
        ]);
    }

    public function mount()
    {
        $this->loadData();
    }

    public $mutasis;

    public function loadData()
    {
        $this->mutasis = Mutasi::orderBy('no_mutasi')->get();
    }

    public function print($id)
    {
        return redirect()->route('mutasi.print', $id);
    }
}
