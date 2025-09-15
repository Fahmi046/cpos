<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuStok extends Model
{
    protected $table = 'kartu_stok';

    protected $fillable = [
        'obat_id',
        'satuan_id',
        'sediaan_id',
        'pabrik_id',
        'penerimaan_id',
        'mutasi_id',
        'jenis',
        'qty',
        'utuhan',
        'ed',
        'batch',
        'tanggal',
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class);
    }

    public function mutasi()
    {
        return $this->belongsTo(Mutasi::class);
    }
}
