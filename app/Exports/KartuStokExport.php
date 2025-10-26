<?php

namespace App\Exports;

use App\Models\KartuStok;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class KartuStokExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start_date;
    protected $end_date;
    protected $obat_id;

    public function __construct($start_date = null, $end_date = null, $obat_id = null)
    {
        $this->start_date = $start_date;
        $this->end_date   = $end_date;
        $this->obat_id    = $obat_id;
    }

    public function collection()
    {
        $query = KartuStok::with([
            'obat.kategori',
            'satuan',
            'sediaan',
            'pabrik',
            'penerimaanDetail'
        ]);

        if ($this->obat_id) {
            $query->where('obat_id', $this->obat_id);
        }

        if ($this->start_date && $this->end_date) {
            $query->whereBetween('tanggal', [$this->start_date, $this->end_date]);
        }

        // Ambil semua data sesuai filter
        $data = $query->orderBy('tanggal', 'desc')->get();

        // Ambil hanya baris terakhir per obat
        $latestPerObat = $data
            ->groupBy('obat_id')
            ->map(function ($items) {
                // Ambil transaksi terbaru
                $latest = $items->sortByDesc('updated_at')->first();

                return [
                    'tanggal'  => $latest->tanggal,
                    'obat'     => $latest->obat?->nama_obat ?? '-',
                    'satuan'   => $latest->utuhan
                        ? ($latest->satuan->nama_satuan ?? '-')
                        : ($latest->sediaan->nama_sediaan ?? '-'),
                    'pabrik'   => $latest->pabrik?->nama_pabrik ?? '-',
                    'kategori' => $latest->obat?->kategori?->nama_kategori ?? '-',
                    'harga'    => $latest->obat?->harga_beli ?? 0,
                    'stok'     => $latest->saldo_akhir ?? 0,
                ];
            })
            ->values();

        return collect($latestPerObat);
    }

    public function headings(): array
    {
        return [
            'Tanggal Terakhir',
            'Obat',
            'Satuan',
            'Pabrik',
            'Kategori',
            'Harga Terakhir',
            'Stok Akhir',
        ];
    }

    public function map($row): array
    {
        return [
            Carbon::parse($row['tanggal'])->format('d-m-Y'),
            $row['obat'],
            $row['satuan'],
            $row['pabrik'],
            $row['kategori'],
            $row['harga'] ? 'Rp ' . number_format($row['harga'], 0, ',', '.') : '-',
            $row['stok'],
        ];
    }
}
