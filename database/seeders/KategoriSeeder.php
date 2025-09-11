<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\KategoriObat;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        KategoriObat::create(['kode_kategori' => '1', 'nama_kategori' => 'Analgesik']);
        KategoriObat::create(['kode_kategori' => '2', 'nama_kategori' => 'Antibiotik']);
        KategoriObat::create(['kode_kategori' => '3', 'nama_kategori' => 'Vitamin']);
    }
}
