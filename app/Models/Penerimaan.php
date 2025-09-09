<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    protected $table = 'penerimaan';

    protected $fillable = [
        'pesanan_id',
        'kreditur_id',
        'tanggal',
        'no_penerimaan',
        'jenis_bayar',
        'no_faktur',
        'tenor',
        'jatuh_tempo',
        'jenis_ppn',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function kreditur()
    {
        return $this->belongsTo(Kreditur::class, 'kreditur_id');
    }

    public function details()
    {
        return $this->hasMany(PenerimaanDetail::class, 'penerimaan_id');
    }
}
