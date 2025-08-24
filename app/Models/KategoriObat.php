<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriObat extends Model
{
    protected $table = 'kategori_obat';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'deskripsi',
        'aktif',
    ];
}
