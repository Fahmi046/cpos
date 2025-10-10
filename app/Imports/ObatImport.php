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

            // 🔹 Kategori
            $kategori = KategoriObat::firstOrCreate(
                ['nama_kategori' => trim($row['kategori'] ?? '-')],
                [
                    'kode_kategori' => $this->generateKodeKategori(),
                    'aktif' => 1,
                ]
            );

            // 🔹 Sediaan
            $sediaan = BentukSediaan::firstOrCreate(
                ['nama_sediaan' => trim($row['sediaan'] ?? '-')],
                [
                    'kode_sediaan' => $this->generateKodeSediaan(),
                    'aktif' => 1,
                ]
            );

            // 🔹 Komposisi
            $komposisi = Komposisi::firstOrCreate(
                ['nama_komposisi' => trim($row['komposisi'] ?? '-')],
                [
                    'kode_komposisi' => $this->generateKodeKomposisi(),
                    'aktif' => 1,
                ]
            );

            // 🔹 Satuan
            $satuan = SatuanObat::firstOrCreate(
                ['nama_satuan' => trim($row['satuan'] ?? '-')],
                [
                    'kode_satuan' => $this->generateKodeSatuan(),
                    'aktif' => 1,
                ]
            );

            // 🔹 Pabrik
            $pabrik = Pabrik::firstOrCreate(
                ['nama_pabrik' => trim($row['pabrik'] ?? '-')],
                [
                    'kode_pabrik' => $this->generateKodePabrik(),
                    'aktif' => 1,
                ]
            );

            // 🔹 Kreditur
            $kreditur = Kreditur::firstOrCreate(
                ['nama' => trim($row['kreditur'] ?? '-')],
                [
                    'kode_kreditur' => $this->generateKodeKreditur(),
                    'aktif' => 1,
                ]
            );

            // 🔹 Simpan / update data obat
            Obat::updateOrCreate(
                ['kode_obat' => $row['kode_obat'] ?? $this->generateKodeObat()],
                [
                    'nama_obat'    => trim($row['nama_obat']),
                    'kategori_id'  => $kategori->id,
                    'sediaan_id'   => $sediaan->id,
                    'komposisi_id' => $komposisi->id,
                    'satuan_id'    => $satuan->id,
                    'pabrik_id'    => $pabrik->id,
                    'kreditur_id'  => $kreditur->id,
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

    // =========================
    // 🔹 Kode Generator Section
    // =========================
    private function generateKodeObat(): string
    {
        $last = Obat::orderBy('id', 'desc')->first();
        $next = $last ? intval(substr($last->kode_obat, 4)) + 1 : 1;
        return 'OBT' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    private function generateKodeKategori(): string
    {
        $last = KategoriObat::orderBy('id', 'desc')->first();
        $next = $last ? intval(substr($last->kode_kategori, 3)) + 1 : 1;
        return 'KAT' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function generateKodeSediaan(): string
    {
        $last = BentukSediaan::orderBy('id', 'desc')->first();
        $next = $last ? intval(substr($last->kode_sediaan, 3)) + 1 : 1;
        return 'SED' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function generateKodeKomposisi(): string
    {
        $last = Komposisi::orderBy('id', 'desc')->first();
        $next = $last ? intval(substr($last->kode_komposisi, 3)) + 1 : 1;
        return 'KOM' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function generateKodeSatuan(): string
    {
        $last = SatuanObat::orderBy('id', 'desc')->first();
        $next = $last ? intval(substr($last->kode_satuan, 3)) + 1 : 1;
        return 'SAT' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function generateKodePabrik(): string
    {
        $last = Pabrik::orderBy('id', 'desc')->first();
        $next = $last ? intval(substr($last->kode_pabrik, 3)) + 1 : 1;
        return 'PAB' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function generateKodeKreditur(): string
    {
        $last = Kreditur::orderBy('id', 'desc')->first();
        $next = $last ? intval(substr($last->kode_kreditur, 3)) + 1 : 1;
        return 'KRD' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
