<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pabrik;

class PabrikSeeder extends Seeder
{
    public function run(): void
    {
        Pabrik::create(['kode_pabrik' => '1', 'nama_pabrik' => 'PT. Farma Sejahtera']);
        Pabrik::create(['kode_pabrik' => '2', 'nama_pabrik' => 'PT. Medika Abadi']);
    }
}
