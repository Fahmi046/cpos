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
    public static function booted()
    {
        // CREATE
        static::created(function ($detail) {
            KartuStok::create([
                'obat_id'              => $detail->obat_id,
                'satuan_id'            => $detail->satuan_id,
                'sediaan_id'           => $detail->sediaan_id,
                'pabrik_id'            => $detail->pabrik_id,
                'penerimaan_id'        => $detail->penerimaan_id,
                'penerimaan_detail_id' => $detail->id,
                'mutasi_id'            => null,
                'mutasi_detail_id'     => null,
                'jenis'                => 'masuk',
                'qty'                  => $detail->qty,
                'utuhan'               => $detail->utuhan ?? 0, // default kalau null
                'ed'                   => $detail->ed,
                'batch'                => $detail->batch,
                'tanggal'              => $detail->penerimaan->tanggal,
                'keterangan'           => 'Penerimaan', // ✅ otomatis isi
            ]);
        });

        // UPDATE
        static::updated(function ($detail) {
            $stok = KartuStok::where('penerimaan_detail_id', $detail->id)->first();

            if ($stok) {
                $stok->update([
                    'satuan_id'  => $detail->satuan_id,
                    'sediaan_id' => $detail->sediaan_id,
                    'pabrik_id'  => $detail->pabrik_id,
                    'qty'        => $detail->qty,
                    'utuhan'     => $detail->utuhan ?? 0,
                    'ed'         => $detail->ed,
                    'batch'      => $detail->batch,
                    'tanggal'    => $detail->penerimaan->tanggal,
                    'keterangan' => 'Penerimaan', // ✅ tetap terjaga jika diupdate
                ]);
            }
        });

        // DELETE
        static::deleted(function ($detail) {
            KartuStok::where('penerimaan_detail_id', $detail->id)->delete();
        });
    }
}
