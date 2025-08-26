<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kreditur extends Model
{
    protected $table = 'kreditur';
    protected $fillable = [
        'kode_kreditur',
        'nama',
        'alamat',
        'telepon',
        'email',
        'aktif'
    ];

    public function obats()
    {
        return $this->hasMany(Obat::class);
    }
}
