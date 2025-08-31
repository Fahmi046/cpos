<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    protected $table = 'pesanan_detail'; // 👈 Nama tabel sesuai migration kamu
    protected $fillable = [
        'pesanan_id',
        'obat_id',
        'qty',
        'harga',
        'jumlah'
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
