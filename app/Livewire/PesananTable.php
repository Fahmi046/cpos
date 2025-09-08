<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Pesanan;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\PesananExport;
use Maatwebsite\Excel\Facades\Excel;

class PesananTable extends Component
{
    use WithPagination;

    protected $listeners = ['refreshTable' => 'loadData'];

    public $search = '';
    public $selectedId;
    public $no_sp, $tanggal, $kategori = '';
    public $details = []; // Tambahkan deklarasi property details

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function edit($id)
    {
        $this->selectedId = $id;
        $pesanan = Pesanan::with('details.obat')->findOrFail($id);

        $this->no_sp = $pesanan->no_sp;
        $this->tanggal = $pesanan->tanggal;
        $this->kategori = $pesanan->kategori;

        $this->details = $pesanan->details->map(function ($detail) {
            return [
                'obat_id' => $detail->obat_id,
                'nama_obat' => $detail->obat->nama_obat ?? '',
                'qty' => $detail->qty,
                'harga' => $detail->harga,
                'jumlah' => $detail->jumlah,
            ];
        })->toArray();

        $this->dispatch('focus-tanggal');
    }



    public function delete($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->details()->delete();
        $pesanan->delete();
        $this->loadData();
        session()->flash('message', 'Pesanan berhasil dihapus.');

        $this->dispatch('refreshKodepesanan');
        $this->dispatch('focus-tanggal');
    }
    public function exportExcel()
    {
        $tanggal = Carbon::today()->format('Y-m-d');
        $fileName = "pesanan_{$tanggal}.xlsx";

        return Excel::download(new PesananExport($this->search), $fileName);
    }

    public function render()
    {
        $pesananList = Pesanan::with('details.obat')
            ->where(function ($query) {
                $query->where('no_sp', 'like', '%' . $this->search . '%')
                    ->orWhere('tanggal', 'like', '%' . $this->search . '%')
                    ->orWhereHas('details.obat', function ($q) {
                        $q->where('nama_obat', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(5);

        return view('livewire.pesanan-table', [
            'pesananList' => $pesananList
        ]);
    }

    public function mount()
    {
        $this->loadData();
    }

    public $pesanans;

    public function loadData()
    {
        $this->pesanans = Pesanan::orderBy('no_sp')->get();
    }

    public function print($id)
    {
        return redirect()->route('pesanan.print', $id);
    }
}
