<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\{BentukSediaan, Pesanan, Penerimaan, Obat, Pabrik, Satuan, SatuanObat, Sediaan};

class PenerimaanForm extends Component
{
    public $penerimaan_id;

    // Header Penerimaan
    public $pesanan_id, $tanggal, $no_penerimaan, $jenis_bayar, $kreditur_id;
    public $no_faktur, $tenor, $jatuh_tempo, $jenis_ppn;

    // DETAIL PENERIMAAN (<<— inilah yang dimaksud $details)
    public $details = [];   // <- WAJIB ada supaya tidak "Undefined variable $details"

    public function mount(?int $penerimaan_id = null)
    {
        $this->penerimaan_id = $penerimaan_id;
        // minimal 1 baris kosong
        $this->details = [$this->emptyDetailRow()];
    }

    protected function emptyDetailRow(): array
    {
        return [
            'obat_id'    => null,
            'pabrik_id'  => null,
            'satuan_id'  => null,
            'sediaan_id' => null,
            'qty'        => 0,
            'ed'         => null,
            'batch'      => '',
            'disc1'      => 0,
            'disc2'      => 0,
            'disc3'      => 0,
            'utuh'       => false, // utuhan atau tidak
        ];
    }

    public function addDetail()
    {
        $this->details[] = $this->emptyDetailRow();
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details); // reindex
    }

    public function loadPesanan()
    {
        if (!$this->pesanan_id) {
            $this->details = [$this->emptyDetailRow()];
            return;
        }

        $pesanan = Pesanan::with(['details'])
            ->find($this->pesanan_id);

        if (!$pesanan) {
            $this->details = [$this->emptyDetailRow()];
            return;
        }

        // mapping detail pesanan -> default detail penerimaan
        $this->details = $pesanan->details->map(function ($d) {
            return [
                'obat_id'    => $d->obat_id,
                'pabrik_id'  => $d->pabrik_id ?? null,
                'satuan_id'  => $d->satuan_id,
                'sediaan_id' => $d->sediaan_id ?? null,
                'qty'        => $d->qty,
                'ed'         => null,
                'batch'      => '',
                'disc1'      => 0,
                'disc2'      => 0,
                'disc3'      => 0,
                'utuh'       => false,
            ];
        })->toArray();
    }

    public function save()
    {
        $this->validate([
            'pesanan_id' => 'required|exists:pesanan,id',
            'tanggal'    => 'required|date',
            'jenis_bayar' => 'required|in:CASH,KREDIT',

            'details'                 => 'required|array|min:1',
            'details.*.obat_id'       => 'required|exists:obat,id',
            'details.*.pabrik_id'     => 'nullable|exists:pabrik,id',
            'details.*.satuan_id'     => 'required|exists:satuan,id',
            'details.*.sediaan_id'    => 'nullable|exists:sediaan,id',
            'details.*.qty'           => 'required|numeric|min:1',
            'details.*.ed'            => 'nullable|date',
            'details.*.batch'         => 'nullable|string|max:50',
            'details.*.disc1'         => 'nullable|numeric|min:0',
            'details.*.disc2'         => 'nullable|numeric|min:0',
            'details.*.disc3'         => 'nullable|numeric|min:0',
            'details.*.utuh'          => 'boolean',
        ]);

        DB::transaction(function () {
            $penerimaan = Penerimaan::updateOrCreate(
                ['id' => $this->penerimaan_id],
                [
                    'pesanan_id'    => $this->pesanan_id,
                    'tanggal'       => $this->tanggal,
                    'no_penerimaan' => $this->no_penerimaan,
                    'jenis_bayar'   => $this->jenis_bayar,
                    'kreditur_id'   => $this->kreditur_id,
                    'no_faktur'     => $this->no_faktur,
                    'tenor'         => $this->tenor,
                    'jatuh_tempo'   => $this->jatuh_tempo,
                    'jenis_ppn'     => $this->jenis_ppn,
                ]
            );

            // sederhana: hapus & buat ulang detail
            $penerimaan->details()->delete();

            foreach ($this->details as $row) {
                $penerimaan->details()->create([
                    'obat_id'    => $row['obat_id'],
                    'pabrik_id'  => $row['pabrik_id'],
                    'satuan_id'  => $row['satuan_id'],
                    'sediaan_id' => $row['sediaan_id'],
                    'qty'        => $row['qty'],
                    'ed'         => $row['ed'],
                    'batch'      => $row['batch'],
                    'disc1'      => $row['disc1'] ?? 0,
                    'disc2'      => $row['disc2'] ?? 0,
                    'disc3'      => $row['disc3'] ?? 0,
                    'utuh'       => !empty($row['utuh']),
                ]);
            }
        });

        session()->flash('success', 'Penerimaan disimpan.');
        return redirect()->route('penerimaan.index');
    }

    public function render()
    {
        return view('livewire.penerimaan-form', [
            'pesananList' => Pesanan::select('id', 'no_sp', 'tanggal')->latest()->get(),
            'obatList'    => Obat::orderBy('nama_obat')->get(),
            'pabrikList'  => Pabrik::orderBy('nama_pabrik')->get(),
            'satuanList'  => SatuanObat::orderBy('nama_satuan')->get(),
            'sediaanList' => BentukSediaan::orderBy('nama_sediaan')->get(),
        ]);
    }
}
