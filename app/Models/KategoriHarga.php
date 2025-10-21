<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriHarga extends Model
{
    use HasFactory;

    // Nama tabel jika berbeda dari plural default
    protected $table = 'kategori_harga';

    // Kolom yang bisa diisi secara massal
    protected $fillable = [
        'nama',
        'persentase',
        'tipe',
    ];
}
