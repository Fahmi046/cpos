<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kreditur;

class KrediturSeeder extends Seeder
{
    public function run(): void
    {
        Kreditur::create([
            'kode_kreditur' => 'KDR-001',
            'nama' => 'PT. Contoh Supplier',
            'alamat' => 'Jl. Contoh No.1',
            'telepon' => '08123456789',
            'aktif' => true
        ]);

        Kreditur::create([
            'kode_kreditur' => 'KDR-002',
            'nama' => 'CV. Sumber Obat',
            'alamat' => 'Jl. Sehat No.10',
            'telepon' => '08198765432',
            'aktif' => true
        ]);
    }
}
