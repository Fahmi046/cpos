<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StokOutlet extends Model
{
    protected $table = 'stok_outlet';

    protected $fillable = [
        'outlet_id',
        'obat_id',
        'satuan_id',
        'sediaan_id',
        'pabrik_id',
        'batch',
        'ed',
        'jenis',
        'masuk',
        'keluar',
        'utuhan',
        'stok_awal',
        'stok_akhir',
        'tanggal',
        'mutasi_id',
        'mutasi_detail_id',
        'penjualan_id',
        'penjualan_detail_id',
        'retur_id',
        'retur_detail_id',
        'keterangan',
    ];

    /* ===============================
       RELASI
    =============================== */

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
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

    public function mutasi()
    {
        return $this->belongsTo(Mutasi::class);
    }

    public function mutasiDetail()
    {
        return $this->belongsTo(MutasiDetail::class, 'mutasi_detail_id');
    }

    // public function penjualan()
    // {
    //     return $this->belongsTo(Penjualan::class);
    // }

    // public function penjualanDetail()
    // {
    //     return $this->belongsTo(PenjualanDetail::class, 'penjualan_detail_id');
    // }

    // public function retur()
    // {
    //     return $this->belongsTo(Retur::class);
    // }

    // public function returDetail()
    // {
    //     return $this->belongsTo(ReturDetail::class, 'retur_detail_id');
    // }

    /* ===============================
       LOGIKA STOK
    =============================== */

    /**
     * Catat pergerakan stok outlet
     */
    public static function recordMovement(array $data)
    {
        return DB::transaction(function () use ($data) {
            $outletId = $data['outlet_id'];
            $obatId   = $data['obat_id'];
            $jenis    = $data['jenis'] ?? 'masuk';
            $qty      = (int)($data['qty'] ?? 0);

            // Tentukan masuk/keluar
            $masuk  = in_array($jenis, ['masuk']) ? $qty : 0;
            $keluar = in_array($jenis, ['keluar', 'retur']) ? $qty : 0;

            // Ambil stok terakhir total semua batch/ED untuk obat ini
            $stokAwal = (int) self::where('outlet_id', $outletId)
                ->where('obat_id', $obatId)
                ->orderBy('id', 'desc')
                ->value('stok_akhir') ?? 0;

            $stokAkhir = $stokAwal + $masuk - $keluar;

            $dataToCreate = array_merge($data, [
                'masuk'      => $masuk,
                'keluar'     => $keluar,
                'stok_awal'  => $stokAwal,
                'stok_akhir' => $stokAkhir,
            ]);

            return self::create($dataToCreate);
        });
    }


    /**
     * Recompute stok_akhir untuk histori
     */
    public static function recomputeStokAkhir(int $outletId, int $obatId)
    {
        $rows = self::where('outlet_id', $outletId)
            ->where('obat_id', $obatId)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $running = 0;
        foreach ($rows as $row) {
            $running = $running + (int)$row->masuk - (int)$row->keluar;

            if ($row->stok_akhir != $running) {
                $row->stok_akhir = $running;
                $row->save();
            }
        }

        return $running; // stok total terakhir
    }
}
