<?php

namespace App\Exports;

use App\Models\KartuStok;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class MutasiExportSummary implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $search;
    protected $start_date;
    protected $end_date;

    public function __construct($search = null, $start_date = null, $end_date = null)
    {
        $this->search = $search;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        // 1️⃣ Ambil ID obat aktif (ada transaksi masuk/keluar di range)
        $obatAktifIds = KartuStok::whereBetween('tanggal', [$this->start_date, $this->end_date])
            ->where(function ($q) {
                $q->where('masuk', '>', 0)
                    ->orWhere('keluar', '>', 0);
            })
            ->pluck('obat_id')
            ->unique();

        // 2️⃣ Hitung total stok_awal, masuk, keluar
        $query = KartuStok::with(['obat', 'satuan', 'sediaan'])
            ->select(
                'obat_id',
                DB::raw('COALESCE(SUM(stok_awal),0) as total_awal'),
                DB::raw('COALESCE(SUM(masuk),0) as total_masuk'),
                DB::raw('COALESCE(SUM(keluar),0) as total_keluar')
            )
            ->whereIn('obat_id', $obatAktifIds)
            ->when($this->search, function ($q) {
                $q->whereHas('obat', fn($sub) => $sub->where('nama_obat', 'like', "%{$this->search}%"));
            })
            ->groupBy('obat_id')
            ->get();

        // 3️⃣ Tambahkan saldo_akhir, nama_satuan, dan HNA (harga obat)
        foreach ($query as $row) {
            $lastRecord = KartuStok::where('obat_id', $row->obat_id)
                ->orderByDesc('id')
                ->first();

            if (!$lastRecord) {
                // Jika tidak ada record sama sekali
                $row->saldo_akhir = 0;
                $row->nama_satuan = '-';
                $row->harga_beli = 0;
                continue;
            }

            // Jika saldo akhir 0/null, tetap 0
            $row->saldo_akhir = empty($lastRecord->saldo_akhir) || $lastRecord->saldo_akhir == 0
                ? 0
                : $lastRecord->saldo_akhir;

            // Nama satuan disesuaikan dengan jenis barang
            $row->nama_satuan = $lastRecord->utuh == 1
                ? ($lastRecord->sediaan->nama_sediaan ?? '-')
                : ($lastRecord->satuan->nama_satuan ?? '-');

            // 🔹 Ambil harga asli dari relasi master obat (tanpa accessor)
            $row->harga_beli = $row->obat?->getRawOriginal('harga_beli') ?? 0;

            // Pastikan total tidak null
            $row->total_awal   = $row->total_awal ?? 0;
            $row->total_masuk  = $row->total_masuk ?? 0;
            $row->total_keluar = $row->total_keluar ?? 0;
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Nama Obat',
            'Satuan',
            'Qty Awal',
            'Qty Masuk',
            'Qty Keluar',
            'Qty Akhir',
            'HNA',
        ];
    }

    public function map($row): array
    {
        return [
            $row->obat->nama_obat ?? '-',
            $row->nama_satuan ?? '-',
            is_numeric($row->total_awal) ? $row->total_awal : 0,
            is_numeric($row->total_masuk) ? $row->total_masuk : 0,
            is_numeric($row->total_keluar) ? $row->total_keluar : 0,
            is_numeric($row->saldo_akhir) ? $row->saldo_akhir : 0,
            is_numeric($row->harga_beli) ? $row->harga_beli : 0,
        ];
    }
}
