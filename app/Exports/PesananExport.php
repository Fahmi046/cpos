<?php

namespace App\Exports;

use App\Models\Pesanan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PesananExport implements FromCollection, WithHeadings
{
    protected $search;

    public function __construct($search = '')
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Pesanan::with('details')
            ->where('no_sp', 'like', '%' . $this->search . '%')
            ->orWhere('tanggal', 'like', '%' . $this->search . '%')
            ->latest()
            ->get()
            ->map(function ($pesanan) {
                return [
                    'No SP' => $pesanan->no_sp,
                    'Tanggal' => $pesanan->tanggal,
                    'Total' => $pesanan->details->sum('jumlah'),
                ];
            });
    }

    public function headings(): array
    {
        return ['No SP', 'Tanggal', 'Total'];
    }
}
