<?php

namespace App\Exports;

use App\Models\Permintaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PermintaanExportDetailed implements FromCollection, WithHeadings, WithMapping
{
    protected $search;
    protected $start_date;
    protected $end_date;

    public function __construct($search = '', $start_date = null, $end_date = null)
    {
        $this->search = $search;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        // Ambil permintaan dengan filter search dan tanggal
        return Permintaan::with(['details.obat', 'details.satuan', 'details.sediaan', 'outlet'])
            ->where(function ($query) {
                $query->where('no_permintaan', 'like', '%' . $this->search . '%')
                    ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            })
            ->whereBetween('tanggal', [$this->start_date, $this->end_date])
            ->orderBy('tanggal', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No Permintaan',
            'Tanggal',
            'Outlet',
            'Keterangan',
            'Nama Obat',
            'Qty',
            'Satuan',
            'Harga',
            'Status'
        ];
    }

    public function map($permintaan): array
    {
        $rows = [];
        foreach ($permintaan->details as $detail) {
            $rows[] = [
                $permintaan->no_permintaan,
                \Carbon\Carbon::parse($permintaan->tanggal)->format('Y-m-d'), // ⬅️ ubah di sini
                $permintaan->outlet->nama_outlet ?? '-',
                $permintaan->keterangan,
                $detail->obat->nama_obat ?? '-',
                $detail->qty_minta,
                $detail->utuhan ? $detail->satuan->nama_satuan ?? '-' : $detail->sediaan->nama_sediaan ?? '-',
                $detail->harga,
                $detail->status
            ];
        }
        return $rows;
    }
}
