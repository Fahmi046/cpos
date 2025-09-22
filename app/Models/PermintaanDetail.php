<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanDetail extends Model
{
    use HasFactory;

    protected $table = 'permintaan_detail';

    protected $fillable = [
        'permintaan_id',
        'obat_id',
        'satuan_id',
        'sediaan_id',
        'pabrik_id',
        'qty_minta',
        'qty_mutasi',
        'utuhan',
        'batch',
        'ed',
        'harga',
        'status',
        'keterangan',
    ];

    // Relasi ke permintaan
    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class);
    }

    // Relasi ke obat
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    // Relasi ke mutasi detail (1 detail permintaan bisa dipenuhi oleh banyak mutasi detail)
    public function mutasiDetails()
    {
        return $this->hasMany(MutasiDetail::class, 'permintaan_detail_id');
    }
}
