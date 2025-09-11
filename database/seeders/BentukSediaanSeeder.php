<?php

namespace Database\Seeders;

use App\Models\BentukSediaan;
use Illuminate\Database\Seeder;
use App\Models\Sediaan;

class BentukSediaanSeeder extends Seeder
{
    public function run(): void
    {
        BentukSediaan::create(['kode_sediaan' => '1', 'nama_sediaan' => 'Kapsul']);
        BentukSediaan::create(['kode_sediaan' => '2', 'nama_sediaan' => 'Sirup']);
    }
}
