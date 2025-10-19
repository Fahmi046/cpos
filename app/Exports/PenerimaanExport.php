<?php

namespace App\Exports;

use App\Models\Penerimaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenerimaanExport implements FromCollection, WithHeadings
{
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        $data = [];

        Penerimaan::with(['kreditur', 'details.obat.satuan', 'details.obat.sediaan'])
            ->when($this->start, fn($q) => $q->whereDate('tanggal', '>=', $this->start))
            ->when($this->end, fn($q) => $q->whereDate('tanggal', '<=', $this->end))
            ->get()
            ->each(function ($penerimaan) use (&$data) {
                foreach ($penerimaan->details as $detail) {
                    $data[] = [
                        'Tanggal Terima' => \Carbon\Carbon::parse($penerimaan->tanggal)->format('d-m-Y'),
                        'Kreditur' => $penerimaan->kreditur->nama ?? '-',
                        'No Faktur' => $penerimaan->no_faktur,
                        'Nama Obat' => $detail->obat->nama_obat ?? '-',
                        'Qty' => $detail->qty,
                        'Satuan' => $detail->utuhan ? ($detail->satuan->nama_satuan ?? '-') : ($detail->obat->sediaan->nama_sediaan ?? '-'),
                        'Harga HNA' => $detail->obat->harga_beli ?? 0,
                        'Harga PPN' => $detail->obat->harga_jual ?? 0, // pastikan ada kolom harga_ppn
                        'Disc 1' => $detail->disc1,
                        'Disc 2' => $detail->disc2,
                        'Disc 3' => $detail->disc3,
                        'ED' => $detail->ed ?? '-',
                        'Batch' => $detail->batch ?? '-',
                    ];
                }
            });

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Tanggal Terima',
            'Kreditur',
            'No Faktur',
            'Nama Obat',
            'Qty',
            'Satuan',
            'Harga HNA',
            'Harga PPN',
            'Disc 1',
            'Disc 2',
            'Disc 3',
            'ED',
            'Batch',
        ];
    }
}
