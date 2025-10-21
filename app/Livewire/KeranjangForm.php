<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Keranjang;
use App\Models\Obat;
use App\Models\KategoriHarga;
use Illuminate\Support\Facades\Auth;

class KeranjangForm extends Component
{
    public $obat_id;
    public $kategori_harga_id;
    public $qty = 1;

    public $keranjangItems = [];

    public function mount()
    {
        $this->loadKeranjang();
    }

    public function render()
    {
        $obats = Obat::all();
        $kategoriHargas = KategoriHarga::all();

        return view('livewire.keranjang-form', compact('obats', 'kategoriHargas'));
    }

    public function addToKeranjang()
    {
        $this->validate([
            'obat_id' => 'required|exists:obat,id',
            'qty' => 'required|integer|min:1',
        ]);

        $obat = Obat::findOrFail($this->obat_id);

        $harga = $obat->harga_jual; // pastikan ada kolom harga_jual di tabel obat

        if ($this->kategori_harga_id) {
            $kategori = KategoriHarga::find($this->kategori_harga_id);
            if ($kategori) {
                if ($kategori->tipe == 'diskon') {
                    $harga -= $harga * $kategori->persentase / 100;
                } else {
                    $harga += $harga * $kategori->persentase / 100;
                }
            }
        }

        Keranjang::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'obat_id' => $this->obat_id,
                'kategori_harga_id' => $this->kategori_harga_id,
            ],
            [
                'qty' => \DB::raw('qty + ' . $this->qty),
                'harga' => $harga,
                'subtotal' => $harga * $this->qty,
            ]
        );

        $this->reset(['obat_id', 'kategori_harga_id', 'qty']);
        $this->loadKeranjang();
    }

    public function removeItem($id)
    {
        Keranjang::where('id', $id)->delete();
        $this->loadKeranjang();
    }

    private function loadKeranjang()
    {
        $this->keranjangItems = Keranjang::with('obat', 'kategoriHarga')
            ->where('user_id', Auth::id())
            ->get();
    }

    public function getTotalProperty()
    {
        return $this->keranjangItems->sum('subtotal');
    }
}
