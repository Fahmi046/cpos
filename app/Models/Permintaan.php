<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasFactory;

    protected $table = 'permintaan';

    protected $fillable = [
        'no_permintaan',
        'tanggal',
        'outlet_id',
        'status',
        'keterangan',
    ];

    // Relasi ke outlet
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // Relasi ke detail permintaan
    public function details()
    {
        return $this->hasMany(PermintaanDetail::class);
    }

    // Relasi ke mutasi (1 permintaan bisa punya banyak mutasi)
    public function mutasi()
    {
        return $this->hasMany(Mutasi::class);
    }
}
