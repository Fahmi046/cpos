<?php

namespace App\Exports;

use App\Models\Mutasi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MutasiExportSummary implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $search;
    protected $start_date;
    protected $end_date;

    public function __construct($search = null, $start_date = null, $end_date = null)
    {
        $this->search = $search;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        $q = Mutasi::with('outlet', 'details.obat');

        if ($this->search) {
            $q->where('no_mutasi', 'like', "%{$this->search}%");
        }

        if ($this->start_date && $this->end_date) {
            $q->whereBetween('tanggal', [$this->start_date, $this->end_date]);
        }

        return $q->orderBy('tanggal')->get();
    }

    public function headings(): array
    {
        return [
            'No Mutasi',
            'Tanggal',
            'Outlet',
            'Keterangan',
            'Detail Mutasi',
        ];
    }

    public function map($mutasi): array
    {
        $details = $mutasi->details->map(function ($d) {
            $nama = $d->obat?->nama_obat ?? '-';
            $qty  = $d->qty ?? 0;
            $harga = $d->harga ?? 0;
            return "{$nama} | Qty: {$qty} | Harga: " . number_format($harga, 0, ',', '.');
        })->implode("\n"); // newline di dalam satu sel

        return [
            $mutasi->no_mutasi,
            Carbon::parse($mutasi->tanggal)->format('d-m-Y'),
            $mutasi->outlet?->nama_outlet ?? '-',
            $mutasi->keterangan,
            $details,
        ];
    }
}
