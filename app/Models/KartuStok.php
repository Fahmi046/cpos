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
        'penerimaan_detail_id',
        'mutasi_id',
        'mutasi_detail_id',
        'jenis',
        'qty',
        'utuhan',
        'ed',
        'batch',
        'tanggal',
        'keterangan',
    ];

    // Relasi utama
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

    // Relasi penerimaan
    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class);
    }
    public function penerimaanDetail()
    {
        return $this->belongsTo(PenerimaanDetail::class, 'penerimaan_detail_id');
    }

    // Relasi mutasi
    public function mutasi()
    {
        return $this->belongsTo(Mutasi::class);
    }
    public function mutasiDetail()
    {
        return $this->belongsTo(MutasiDetail::class, 'mutasi_detail_id');
    }
}
