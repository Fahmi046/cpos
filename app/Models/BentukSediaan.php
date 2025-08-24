<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BentukSediaan extends Model
{
    protected $table = 'bentuk_sediaans';

    // Kolom yang boleh diisi dengan mass assignment
    protected $fillable = [
        'id',             // tambahkan id karena dipakai di updateOrCreate
        'kode_sediaan',
        'nama_sediaan',
        'deskripsi',
        'aktif',
    ];
}
