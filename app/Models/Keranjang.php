<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;

    protected $table = 'keranjang';

    protected $fillable = [
        'user_id',
        'obat_id',
        'kategori_harga_id',
        'qty',
        'harga',
        'subtotal',
        'outlet_id',
    ];

    // Relasi ke User (kasir)
    public function user()
    {
        return $this->belongsTo(User::class);
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
