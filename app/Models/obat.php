<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obat';
    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'kategori_id',
        'satuan_id',
        'sediaan_id',
        'pabrik_id',
        'kreditur_id',
        'komposisi_id',
        'harga_beli',
        'harga_jual',
        'isi_obat',
        'dosis',
        'utuh_satuan',
        'prekursor',
        'psikotropika',
        'resep_active',
        'aktif',
        'stok_awal',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriObat::class, 'kategori_id');
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
    public function komposisi()
    {
        return $this->belongsTo(Komposisi::class, 'komposisi_id');
    }

    public function kreditur()
    {
        return $this->belongsTo(kreditur::class, 'kreditur_id');
    }
}
