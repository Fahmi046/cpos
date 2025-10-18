<?php

namespace App\Exports;

use App\Models\KartuStok;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StokOutletExport implements FromCollection, WithHeadings, WithMapping
{
    protected $outlet_id, $start, $end;

    public function __construct($outlet_id, $start, $end)
    {
        $this->outlet_id = $outlet_id;
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        return KartuStok::with(['obat', 'outlet'])
            ->where('outlet_id', $this->outlet_id)
            ->whereBetween('tanggal', [$this->start, $this->end])
            ->orderBy('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Outlet',
            'Obat',
            'Batch',
            'ED',
            'Stok Awal',
            'Masuk',
            'Keluar',
            'Saldo Akhir',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal,
            $row->outlet?->nama_outlet ?? '-',
            $row->obat?->nama_obat ?? '-',
            $row->batch ?? '-',
            $row->ed ?? '-',
            $row->stok_awal ?? 0,
            $row->masuk ?? 0,
            $row->keluar ?? 0,
            $row->saldo_akhir ?? 0,
            $row->keterangan ?? '-',
        ];
    }
}
