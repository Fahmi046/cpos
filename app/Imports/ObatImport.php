<?php

namespace App\Imports;

use App\Models\Obat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ObatImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $kode = $row['kode_obat'] ?? $this->generateKodeObat();

            Obat::updateOrCreate(
                ['kode_obat' => $kode],
                [
                    'nama_obat'     => $row['nama_obat'] ?? null,
                    'kategori_id'   => $row['kategori_id'] ?? null,
                    'satuan_id'     => $row['satuan_id'] ?? null,
                    'sediaan_id'    => $row['sediaan_id'] ?? null,
                    'pabrik_id'     => $row['pabrik_id'] ?? null,
                    'kreditur_id'   => $row['kreditur_id'] ?? null,
                    'komposisi_id'  => $row['komposisi_id'] ?? null,
                    'harga_beli'    => $row['harga_beli'] ?? 0,
                    'harga_jual'    => $row['harga_jual'] ?? 0,
                    'isi_obat'      => $row['isi_obat'] ?? null,
                    'dosis'         => $row['dosis'] ?? null,
                    'utuh_satuan'   => $row['utuh_satuan'] ?? 0,
                    'prekursor'     => $row['prekursor'] ?? 0,
                    'psikotropika'  => $row['psikotropika'] ?? 0,
                    'resep_active'  => $row['resep_active'] ?? 0,
                    'aktif'         => $row['aktif'] ?? 1,
                ]
            );
        }
    }

    private function generateKodeObat(): string
    {
        $last = Obat::orderBy('id', 'desc')->first();
        $nextNumber = $last ? intval(substr($last->kode_obat, 4)) + 1 : 1;
        return '0010' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
