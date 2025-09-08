<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    protected $table = 'pesanan_detail';

    protected $fillable = [
        'pesanan_id',
        'obat_id',
        'satuan_id',
        'sediaan_id',
        'pabrik_id',
        'qty',
        'harga',
        'jumlah',
        'utuhan' // boolean
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    public function satuan()
    {
        return $this->belongsTo(SatuanObat::class, 'satuan_id');
    }

    public function sediaan()
    {
        return $this->belongsTo(BentukSediaan::class, 'sediaan_id');
    }

    public function pabrik()
    {
        return $this->belongsTo(Pabrik::class, 'pabrik_id');
    }
}
