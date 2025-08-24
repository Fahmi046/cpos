<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pabrik extends Model
{
    protected $table = 'pabrik';

    protected $fillable = [
        'id',
        'kode_pabrik',
        'nama_pabrik',
        'alamat',
        'telepon',
        'aktif',
    ];
}
