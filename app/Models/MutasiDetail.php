<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiDetail extends Model
{
    protected $table = 'mutasi_detail';

    protected $fillable = [
        'mutasi_id',
        'obat_id',
        'satuan_id',
        'sediaan_id',
        'pabrik_id',
        'permintaan_detail_id',
        'qty',
        'utuhan',
        'batch',
        'ed',
        'harga',
    ];

    protected $casts = [
        'ed' => 'date',
    ];

    // ================== RELASI ================== //
    public function mutasi()
    {
        return $this->belongsTo(Mutasi::class, 'mutasi_id');
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
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

    public function penerimaanDetail()
    {
        return $this->belongsTo(PenerimaanDetail::class, 'penerimaan_detail_id');
    }

    public function permintaanDetail()
    {
        return $this->belongsTo(PermintaanDetail::class, 'permintaan_detail_id');
    }

    public function stokOutlet()
    {
        return $this->hasOne(StokOutlet::class, 'mutasi_detail_id');
    }
}
