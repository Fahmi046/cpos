<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komposisi extends Model
{
    protected $table = 'komposisi'; // atau "komposisi", sesuaikan migration

    protected $fillable = [
        'kode_komposisi',
        'nama_komposisi',
        'deskripsi',
    ];
}
