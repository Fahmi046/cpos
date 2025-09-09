<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaanDetail extends Model
{
    protected $table = 'penerimaan_detail';

    protected $fillable = [
        'penerimaan_id',
        'obat_id',
        'satuan_id',
        'sediaan_id',
        'pabrik_id',
        'qty',
        'utuhan',
        'ed',
        'batch',
        'harga',
        'disc1',
        'disc2',
        'disc3',
        'subtotal',
    ];

    protected $casts = [
        'utuh' => 'boolean',
        'ed' => 'date',
        'harga' => 'decimal:2',
        'disc1' => 'decimal:2',
        'disc2' => 'decimal:2',
        'disc3' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class, 'penerimaan_id');
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

    protected static function booted()
    {
        static::saving(function ($detail) {
            $harga = floatval($detail->harga ?? 0);
            $qty   = intval($detail->qty ?? 0);
            $total = $harga * $qty;

            // Anggap disc1/2/3 adalah persen (0-100). Diskon berlapis.
            if ($detail->disc1 > 0) {
                $total -= $total * ($detail->disc1 / 100);
            }
            if ($detail->disc2 > 0) {
                $total -= $total * ($detail->disc2 / 100);
            }
            if ($detail->disc3 > 0) {
                $total -= $total * ($detail->disc3 / 100);
            }

            $detail->subtotal = round($total, 2);
        });
    }
}
