<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $table = 'outlets';

    protected $fillable = [
        'kode_outlet',
        'nama_outlet',
        'alamat',
        'telepon',
        'pic',
        'aktif',
    ];

    public function mutasi()
    {
        return $this->hasMany(Mutasi::class, 'outlet_id');
    }
}
