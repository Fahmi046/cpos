<?php

namespace App\Models;

use App\Models\SatuanObat;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obat'; // <- wajib kalau nama tabel bukan jamak
    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'kategori',
        'bentuk_sediaan',
        'kandungan',
        'harga_beli',
        'harga_jual',
        'stok',
        'satuan',
        'pabrik',
        'tgl_expired',
    ];
    // app/Models/Obat.php
    public function satuan()
    {
        return $this->belongsTo(SatuanObat::class, 'satuan_id');
    }
}
