<?php

namespace App\Exports;

use App\Models\PesananDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class PesananExport implements FromQuery, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return PesananDetail::with(['pesanan', 'obat.pabrik', 'obat.satuan', 'obat.sediaan', 'obat.kreditur'])
            ->whereHas('pesanan', function ($q) {
                // filter tanggal hari ini
                $q->whereDate('tanggal', Carbon::today());

                // optional: filter pencarian jika ada
                if ($this->search) {
                    $q->where(function ($sub) {
                        $sub->where('no_sp', 'like', '%' . $this->search . '%')
                            ->orWhere('tanggal', 'like', '%' . $this->search . '%');
                    });
                }
            });
    }

    public function headings(): array
    {
        return [
            'No SP',
            'Tanggal',
            'Nama Obat',
            'Nama Pabrik',
            'Satuan',
            'Qty',
            'Harga',
            'Jumlah',
            'Kreditur'
        ];
    }

    public function map($detail): array
    {
        $satuan = $detail->utuhan
            ? ($detail->obat->satuan->nama_satuan ?? 'PCS')
            : ($detail->obat->sediaan->nama_sediaan ?? 'PCS');

        return [
            $detail->pesanan->no_sp ?? '-',
            $detail->pesanan->tanggal ?? '-',
            $detail->obat->nama_obat ?? '-',
            $detail->obat->pabrik->nama_pabrik ?? '-',
            $satuan,
            $detail->qty,
            $detail->harga,
            $detail->jumlah,
            $detail->obat->kreditur->nama ?? '-',
        ];
    }
}
