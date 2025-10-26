<?php

namespace App\Exports;

use App\Models\KartuStok;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class KartuStokExportAll implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = KartuStok::with([
            'obat.kategori',
            'satuan',
            'sediaan',
            'pabrik',
            'penerimaanDetail',
        ])
            ->orderBy('obat_id')
            ->orderBy('tanggal', 'desc')
            ->get();

        // Ambil data unik berdasarkan nama_obat + batch + ed
        $grouped = $query->groupBy(function ($item) {
            return ($item->obat?->nama_obat ?? '-') . '|' .
                ($item->batch ?? '-') . '|' .
                ($item->ed ?? '-');
        });

        $data = $grouped->map(function ($items) {
            // Hitung total masuk dan keluar
            $totalMasuk  = $items->sum('masuk');
            $totalKeluar = $items->sum('keluar');

            // Ambil record terbaru
            $latest = $items->sortByDesc('tanggal')->first();

            return [
                'tanggal'  => $latest->tanggal,
                'obat'     => $latest->obat?->nama_obat ?? '-',
                'batch'    => $latest->batch ?? '-',
                'ed'       => $latest->ed ? Carbon::parse($latest->ed)->format('d-m-Y') : '-',
                'satuan'   => $latest->utuhan
                    ? ($latest->satuan->nama_satuan ?? '-')
                    : ($latest->sediaan->nama_sediaan ?? '-'),
                'pabrik'   => $latest->pabrik?->nama_pabrik ?? '-',
                'kategori' => $latest->obat?->kategori?->nama_kategori ?? '-',
                'harga'    => $latest->obat?->harga_beli ?? 0,
                'masuk'    => $totalMasuk,
                'keluar'   => $totalKeluar,
                'stok'     => $totalMasuk - $totalKeluar,
            ];
        })->values();

        return $data;
    }

    public function headings(): array
    {
        return [
            'Tanggal Terakhir',
            'Nama Obat',
            'Batch',
            'ED',
            'Satuan',
            'Pabrik',
            'Kategori',
            'Harga Terakhir',
            'Total Masuk',
            'Total Keluar',
            'Stok Akhir (Masuk - Keluar)',
        ];
    }

    public function map($row): array
    {
        return [
            Carbon::parse($row['tanggal'])->format('d-m-Y'),
            $row['obat'],
            $row['batch'],
            $row['ed'],
            $row['satuan'],
            $row['pabrik'],
            $row['kategori'],
            $row['harga'] ? 'Rp ' . number_format($row['harga'], 0, ',', '.') : '-',
            $row['masuk'],
            $row['keluar'],
            $row['stok'],
        ];
    }
}
