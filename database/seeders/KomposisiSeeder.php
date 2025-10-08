<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Komposisi;

class KomposisiSeeder extends Seeder
{
    public function run(): void
    {
        Komposisi::create([
            'kode_komposisi' => '001',
            'nama_komposisi' => 'Paracetamol'
        ]);
        Komposisi::create([
            'kode_komposisi' => '002',
            'nama_komposisi' => 'Amoxicillin'
        ]);
        Komposisi::create([
            'kode_komposisi' => '003',
            'nama_komposisi' => 'Vitamin C'
        ]);
    }
}
