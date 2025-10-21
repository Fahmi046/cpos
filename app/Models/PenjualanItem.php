<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanItem extends Model
{
    use HasFactory;

    protected $table = 'penjualan_detail'; // pastikan ini sesuai tabel

    protected $fillable = [
        'penjualan_id',
        'obat_id',
        'kategori_harga_id',
        'qty',
        'harga',
        'subtotal',
        'outlet_id',
    ];

    // Relasi ke Penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    // Relasi ke Obat
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    // Relasi ke KategoriHarga
    public function kategoriHarga()
    {
        return $this->belongsTo(KategoriHarga::class);
    }

    // Relasi ke Outlet
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
