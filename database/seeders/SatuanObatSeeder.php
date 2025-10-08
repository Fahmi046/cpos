<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SatuanObat;

class SatuanObatSeeder extends Seeder
{
    public function run(): void
    {
        SatuanObat::create([
            'kode_satuan' => '1',
            'nama_satuan' => 'Tablet',
        ]);
        SatuanObat::create([
            'kode_satuan' => '2',
            'nama_satuan' => 'Botol',
        ]);
    }
}
