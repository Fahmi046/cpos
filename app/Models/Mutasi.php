<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    protected $table = 'mutasi';

    protected $fillable = [
        'no_mutasi',
        'tanggal',
        'outlet_id',
        'keterangan',
    ];

    // ================== RELASI ================== //
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function details()
    {
        return $this->hasMany(MutasiDetail::class, 'mutasi_id');
    }
}
