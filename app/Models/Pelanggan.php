<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan'; // nama tabel sesuai migration

    protected $fillable = [
        'nama',
        'no_hp',
        'alamat',
        'email',
    ];

    // Relasi ke Penjualan
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }
}
