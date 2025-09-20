<?php

namespace App\Exports;

use App\Models\Mutasi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MutasiExportDetailed implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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

        // flatMap -> kembalikan koleksi baris, satu baris per detail
        return $q->orderBy('tanggal')->get()->flatMap(function ($mutasi) {
            return $mutasi->details->map(function ($d) use ($mutasi) {
                return [
                    'no_mutasi'  => $mutasi->no_mutasi,
                    'tanggal'    => $mutasi->tanggal,
                    'outlet'     => $mutasi->outlet?->nama_outlet ?? '-',
                    'keterangan' => $mutasi->keterangan,
                    'obat'       => $d->obat?->nama_obat ?? '-',
                    'batch'      => $d->batch ?? '-',
                    'ed'         => $d->ed ?? '-',
                    'qty'        => $d->qty ?? 0,
                    'harga'      => $d->harga ?? 0,
                    'jumlah'     => ($d->qty ?? 0) * ($d->harga ?? 0),
                ];
            });
        });
    }

    public function headings(): array
    {
        return [
            'No Mutasi',
            'Tanggal',
            'Outlet',
            'Keterangan',
            'Obat',
            'Batch',
            'ED',
            'Qty',
            'Harga',
            'Jumlah',
        ];
    }

    // $row di sini adalah array yang kita buat di collection()
    public function map($row): array
    {
        return [
            $row['no_mutasi'],
            Carbon::parse($row['tanggal'])->format('d-m-Y'),
            $row['outlet'],
            $row['keterangan'],
            $row['obat'],
            $row['batch'],
            $row['ed'],
            $row['qty'],
            $row['harga'],
            $row['jumlah'],
        ];
    }
}
