<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiDetail extends Model
{
    protected $table = 'mutasi_detail';

    protected $fillable = [
        'mutasi_id',
        'obat_id',
        'satuan_id',
        'sediaan_id',
        'pabrik_id',
        'qty',
        'batch',
        'ed',
    ];

    // ================== RELASI ================== //
    public function mutasi()
    {
        return $this->belongsTo(Mutasi::class, 'mutasi_id');
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }

    public function satuan()
    {
        return $this->belongsTo(SatuanObat::class, 'satuan_id');
    }

    public function sediaan()
    {
        return $this->belongsTo(BentukSediaan::class, 'sediaan_id');
    }

    public function pabrik()
    {
        return $this->belongsTo(Pabrik::class, 'pabrik_id');
    }

    // Auto update ke kartu stok
    protected static function booted()
    {
        // saat dibuat
        static::created(function ($detail) {
            KartuStok::create([
                'obat_id'    => $detail->obat_id,
                'mutasi_id'  => $detail->mutasi_id,
                'penerimaan_id' => null,
                'tanggal'    => now(),
                'jenis'      => 'keluar',
                'qty'        => $detail->qty,
            ]);
        });

        // saat diupdate
        static::updated(function ($detail) {
            $stok = KartuStok::where('obat_id', $detail->obat_id)
                ->where('mutasi_id', $detail->mutasi_id)
                ->first();

            if ($stok) {
                $stok->update([
                    'qty'     => $detail->qty,
                    'tanggal' => now(),
                ]);
            }
        });

        // saat dihapus
        static::deleted(function ($detail) {
            KartuStok::where('obat_id', $detail->obat_id)
                ->where('mutasi_id', $detail->mutasi_id)
                ->delete();
        });
    }
}
