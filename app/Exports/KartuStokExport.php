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

    // Penampung saldo per obat
    protected $saldoPerObat = [];

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

        return $query->orderBy('tanggal', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Obat',
            'Batch',
            'ED',
            'Satuan',
            'Pabrik',
            'Kategori',
            'Harga',
            'Masuk',
            'Keluar',
            'Stok Akhir',
        ];
    }

    public function map($row): array
    {
        $key = $row->obat_id . '-' . $row->batch . '-' . $row->ed;

        if (!isset($this->saldoPerObat[$key])) {
            $this->saldoPerObat[$key] = $row->stok_awal ?? 0;
        }

        // update saldo berdasarkan kolom masuk dan keluar
        $this->saldoPerObat[$key] += ($row->masuk ?? 0);
        $this->saldoPerObat[$key] -= ($row->keluar ?? 0);

        return [
            \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y'),
            $row->obat?->nama_obat ?? '-',
            $row->batch ?? '-',
            $row->ed ? \Carbon\Carbon::parse($row->ed)->format('d-m-Y') : '-',
            $row->utuhan
                ? ($row->satuan->nama_satuan ?? '-')
                : ($row->sediaan->nama_sediaan ?? '-'),
            $row->pabrik?->nama_pabrik ?? '-',
            $row->obat?->kategori?->nama_kategori ?? '-',
            $row->penerimaanDetail?->harga
                ? 'Rp ' . number_format($row->penerimaanDetail->harga, 0, ',', '.')
                : '-',
            $row->masuk ?? '-',
            $row->keluar ?? '-',
            $this->saldoPerObat[$key],
        ];
    }
}
