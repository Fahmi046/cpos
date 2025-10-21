<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'user_id',
        'pelanggan_id',
        'dokter_id',
        'outlet_id',
        'total',
        'bayar',
        'kembalian',
        'metode_pembayaran',
    ];

    // Relasi ke User (kasir)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    // Relasi ke Dokter
    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    // Relasi ke Outlet
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // Relasi ke detail penjualan (jika ada tabel penjualan_detail)
    public function details()
    {
        return $this->hasMany(PenjualanItem::class);
    }
}
