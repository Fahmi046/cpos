<?php

namespace App\Imports;

use App\Models\Obat;
use App\Models\KategoriObat;
use App\Models\BentukSediaan;
use App\Models\Komposisi;
use App\Models\SatuanObat;
use App\Models\Pabrik;
use App\Models\Kreditur;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ObatImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $kategori = KategoriObat::where('nama_kategori', $row['kategori'])->first();
            $sediaan = BentukSediaan::where('nama_sediaan', $row['sediaan'])->first();
            $komposisi = Komposisi::where('nama_komposisi', $row['komposisi'])->first();
            $satuan = SatuanObat::where('nama_satuan', $row['satuan'])->first();
            $pabrik = Pabrik::where('nama_pabrik', $row['pabrik'])->first();
            $kreditur = Kreditur::where('nama', $row['kreditur'])->first();

            Obat::updateOrCreate(
                ['kode_obat' => $row['kode_obat'] ?? $this->generateKodeObat()],
                [
                    'nama_obat'    => $row['nama_obat'],
                    'kategori_id'  => $kategori?->id,
                    'sediaan_id'   => $sediaan?->id,
                    'komposisi_id' => $komposisi?->id,
                    'satuan_id'    => $satuan?->id,
                    'pabrik_id'    => $pabrik?->id,
                    'kreditur_id'  => $kreditur?->id,
                    'harga_beli'   => $row['harga_beli'] ?? 0,
                    'harga_jual'   => $row['harga_jual'] ?? 0,
                    'isi_obat'     => $row['isi_obat'] ?? null,
                    'dosis'        => $row['dosis'] ?? null,
                    'utuh_satuan'  => $row['utuh_satuan'] ?? null,
                    'prekursor'    => $row['prekursor'] ?? 0,
                    'psikotropika' => $row['psikotropika'] ?? 0,
                    'resep_active' => $row['resep_active'] ?? 0,
                    'aktif'        => $row['aktif'] ?? 1,
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
