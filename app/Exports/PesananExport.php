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
            ->when($this->search, function ($query) {
                $query->whereHas('pesanan', function ($q) {
                    $q->where('no_sp', 'like', '%' . $this->search . '%')
                        ->orWhere('tanggal', 'like', '%' . $this->search . '%');
                })->orWhereHas('obat', function ($q) {
                    $q->where('nama_obat', 'like', '%' . $this->search . '%');
                });
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
        // Tentukan satuan sesuai utuhan
        $satuan = $detail->utuhan
            ? ($detail->obat->satuan->nama_satuan ?? 'PCS')
            : ($detail->obat->sediaan->nama_sediaan ?? 'PCS');

        // Hitung qty sesuai utuhan
        $qty = $detail->utuhan && $detail->obat->isi_obat
            ? intval($detail->qty / $detail->obat->isi_obat)
            : $detail->qty;

        return [
            $detail->pesanan->no_sp ?? '-',
            $detail->pesanan->tanggal ? Carbon::parse($detail->pesanan->tanggal)->format('d-m-Y') : '-',
            $detail->obat->nama_obat ?? '-',
            $detail->obat->pabrik->nama_pabrik ?? '-',
            $satuan,
            $qty,
            $detail->harga ?? 0,
            $detail->jumlah ?? 0,
            $detail->obat->kreditur->nama ?? '-',
        ];
    }
}
