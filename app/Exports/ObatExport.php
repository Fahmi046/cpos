<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ObatExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        return Obat::select([
            'kode_obat',
            'nama_obat',
            'kategori_id',
            'satuan_id',
            'sediaan_id',
            'pabrik_id',
            'kreditur_id',
            'komposisi_id',
            'harga_beli',
            'harga_jual',
            'isi_obat',
            'dosis',
            'utuh_satuan',
            'prekursor',
            'psikotropika',
            'resep_active',
            'aktif',
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Kode Obat',
            'Nama Obat',
            'Kategori ID',
            'Satuan ID',
            'Sediaan ID',
            'Pabrik ID',
            'Kreditur ID',
            'Komposisi ID',
            'Harga Beli',
            'Harga Jual',
            'Isi Obat',
            'Dosis',
            'Utuh Satuan',
            'Prekursor',
            'Psikotropika',
            'Resep Active',
            'Aktif',
        ];
    }
}
