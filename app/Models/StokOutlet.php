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
        'qty',
        'utuhan',
        'stok_akhir',
        'tanggal',
        'mutasi_id',
        'mutasi_detail_id',
        'penjualan_id',
        'penjualan_detail_id',
        'retur_id',
        'retur_detail_id',
    ];

    // relasi...
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    /**
     * Record a movement in stok_outlet.
     * $data must contain: outlet_id, obat_id, qty (positive number),
     * jenis = 'masuk'|'keluar'|'retur' ; batch, ed, tanggal, refs...
     *
     * We store qty as signed: masuk => +qty, keluar/retur => -qty.
     * This function computes stok_akhir based on last record for same group.
     */
    public static function recordMovement(array $data)
    {
        return DB::transaction(function () use ($data) {
            $outletId = $data['outlet_id'];
            $obatId   = $data['obat_id'];
            $batch    = $data['batch'] ?? null;
            $ed       = $data['ed'] ?? null;
            $jenis    = $data['jenis'] ?? 'masuk';
            $rawQty   = (int)($data['qty'] ?? 0);

            // signed qty convention
            $signedQty = in_array($jenis, ['keluar', 'retur']) ? -abs($rawQty) : abs($rawQty);

            // get last stok_akhir for same key (group by outlet, obat, batch, ed)
            $lastQuery = self::where('outlet_id', $outletId)
                ->where('obat_id', $obatId);

            if ($batch === null) {
                $lastQuery = $lastQuery->whereNull('batch');
            } else {
                $lastQuery = $lastQuery->where('batch', $batch);
            }
            if ($ed !== null) $lastQuery = $lastQuery->where('ed', $ed);

            $lastStok = (int) $lastQuery->orderBy('id', 'desc')->value('stok_akhir') ?? 0;

            $newStok = $lastStok + $signedQty;

            $dataToCreate = array_merge($data, [
                'qty' => $signedQty,
                'stok_akhir' => $newStok,
            ]);

            return self::create($dataToCreate);
        });
    }

    /**
     * Recompute stok_akhir for a given group (outlet, obat, optional batch + ed).
     * Use this after removing / reordering historical records.
     */
    public static function recomputeStokAkhir(int $outletId, int $obatId, $batch = null, $ed = null)
    {
        $query = self::where('outlet_id', $outletId)->where('obat_id', $obatId);

        if ($batch === null) {
            $query = $query->whereNull('batch');
        } else {
            $query = $query->where('batch', $batch);
        }
        if ($ed !== null) $query = $query->where('ed', $ed);

        $rows = $query->orderBy('tanggal')->orderBy('id')->get();

        $running = 0;
        foreach ($rows as $row) {
            $running += (int) $row->qty;
            // update stok_akhir only if different (saves writes)
            if ($row->stok_akhir != $running) {
                $row->stok_akhir = $running;
                $row->save();
            }
        }

        return $running; // final stock
    }
}
