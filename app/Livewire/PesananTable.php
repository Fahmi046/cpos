<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pesanan;

class PesananTable extends Component
{
    public function render()
    {
        return view('livewire.pesanan-table', [
            'pesananList' => Pesanan::with('details')->latest()->get()
        ]);
    }
}
