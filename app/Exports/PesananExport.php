<?php

namespace App\Exports;

use App\Models\Pesanan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PesananExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Pesanan::with([
            'details.obat.pabrik',
            'details.obat.satuan',
            'details.obat.sediaan',
            'details.obat.kreditur'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'No',
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

    public function map($pesanan): array
    {
        $rows = [];

        foreach ($pesanan->details as $detail) {
            $satuan = $detail->utuhan
                ? ($detail->obat->satuan->nama_satuan ?? 'PCS')
                : ($detail->obat->sediaan->nama_satuan ?? 'PCS');

            $rows[] = [
                $pesanan->id,
                $pesanan->no_sp,
                $pesanan->tanggal,
                $detail->obat->nama_obat ?? '-',
                $detail->obat->pabrik->nama_pabrik ?? '-',
                $satuan,
                $detail->qty,
                $detail->harga,
                $detail->jumlah,
                $detail->obat->kreditur->nama ?? '-', // ✅ Tambahan Kreditur
            ];
        }

        return $rows;
    }
}
