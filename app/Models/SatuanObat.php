<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SatuanObat extends Model
{
    use HasFactory;

    protected $table = 'satuan_obat';

    protected $fillable = [
        'kode_satuan',
        'nama_satuan',
        'deskripsi',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    // Jika Anda punya model Obat dan kolom foreign key satuan_id:
    public function obats()
    {
        return $this->hasMany(Obat::class, 'satuan_id');
    }
}
