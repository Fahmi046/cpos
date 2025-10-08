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
        'permintaan_id',   // tambahkan ini
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

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id');
    }

    public function stokOutlets()
    {
        return $this->hasMany(StokOutlet::class, 'mutasi_id');
    }

    // ================== EVENT HOOK ================== //
    protected static function booted()
    {
        static::deleting(function ($mutasi) {
            // kalau mutasi dihapus, stok outlet ikut terhapus
            $mutasi->stokOutlets()->delete();
        });
    }
}
