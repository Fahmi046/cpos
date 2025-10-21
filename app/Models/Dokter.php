<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;

    // Nama tabel jika berbeda dari plural default (dokters)
    protected $table = 'dokter';

    // Kolom yang bisa diisi secara massal
    protected $fillable = [
        'nama',
        'no_hp',
        'alamat',
        'spesialis',
    ];

    // Jika ingin menonaktifkan timestamps
    // public $timestamps = false;
}
