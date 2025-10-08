    <?php

    namespace App\Livewire;

    use Livewire\Component;
    use App\Models\Pesanan;
    use App\Models\PesananDetail;
    use App\Models\Obat;
    use Carbon\Carbon;

    class PesananForm extends Component
    {
        public $pesanan_id;
        public $no_sp;
        public $tanggal;
        public $kategori = '';
        public $kategoriList = [
            'UMUM' => 'UMUM',
            'RS' => 'RS',
            'GROSIR' => 'GROSIR'
        ];
        public $details = [];
        protected $branchCode = 'APT-SHBT';

        public function mount()
        {
            $this->tanggal = date('Y-m-d');
            $this->kategori = 'UMUM'; // Default kategori
            $this->details = [
                ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0]
            ];
            $this->obatList = Obat::all();
            $this->no_sp = $this->generateNoSp();
        }

        private $kategoriCodes = [
            'OOT' => 'SPOT',
            'PREK' => 'SPPR',
            'PSIKO' => 'SPP',
            'NARKO' => 'SPN',
        ];

        public function generateNoSp()
        {
            $tanggal = Carbon::parse($this->tanggal);
            $year = $tanggal->format('Y');
            $month = $this->monthToRoman((int)$tanggal->format('n'));

            // Ambil kode kategori dari mapping
            $kategoriCode = $this->kategoriCodes[$this->kategori] ?? $this->kategori;

            // Hitung urutan SP tahun ini
            $count = Pesanan::whereYear('tanggal', $year)->count() + 1;
            $seq = str_pad($count, 4, '0', STR_PAD_LEFT);

            // Format SP
            return "SP-{$seq}/{$this->branchCode}/{$kategoriCode}/{$month}/{$year}";
        }


        private function monthToRoman($month)
        {
            $map = [
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                7 => 'VII',
                8 => 'VIII',
                9 => 'IX',
                10 => 'X',
                11 => 'XI',
                12 => 'XII'
            ];
            return $map[$month] ?? '';
        }

        public function updatedTanggal()
        {
            $this->no_sp = $this->generateNoSp();
        }

        public function updatedKategori($value)
        {
            $this->no_sp = $this->generateNoSp();
        }

        public function addDetail()
        {
            $this->details[] = ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0];
            $lastIndex = count($this->details) - 1;

            $this->dispatch('focus-row', index: $lastIndex);
        }

        public function removeDetail($index)
        {
            unset($this->details[$index]);
            $this->details = array_values($this->details);
        }

        public function updatedDetails($value, $name)
        {
            foreach ($this->details as $i => $detail) {

                // Jika obat_id berubah → ambil data obat dari DB
                if (!empty($detail['obat_id'])) {
                    $obat = Obat::find($detail['obat_id']);
                    if ($obat) {
                        $this->details[$i]['harga'] = $obat->harga_jual;
                        $this->details[$i]['isi'] = $obat->isi_obat ?? 1;
                        $this->details[$i]['satuan'] = $obat->satuan->nama_satuan ?? '';
                    }
                }

                // Hitung jumlah otomatis saat qty atau harga berubah
                $qty = $detail['qty'] ?? 0;
                $harga = $detail['harga'] ?? 0;
                $isi = $detail['isi'] ?? 1;

                $this->details[$i]['jumlah'] = $qty * $harga;
            }
        }

        public function save()
        {
            $this->validate([
                'no_sp' => 'required|unique:pesanan,no_sp,' . $this->selectedId,
                'tanggal' => 'required|date',
                'details.*.obat_id' => 'required|exists:obat,id',
                'details.*.qty' => 'required|numeric|min:1',
                'details.*.harga' => 'required|numeric|min:0',
            ]);

            if ($this->selectedId) {
                // Update
                $pesanan = Pesanan::find($this->selectedId);
                $pesanan->update([
                    'no_sp' => $this->no_sp,
                    'tanggal' => $this->tanggal,
                    'kategori' => $this->kategori,
                ]);

                $pesanan->details()->delete();
                foreach ($this->details as $detail) {
                    $pesanan->details()->create($detail);
                }

                session()->flash('message', 'Pesanan berhasil diperbarui!');
            } else {
                // Insert baru
                $pesanan = Pesanan::create([
                    'no_sp' => $this->no_sp,
                    'tanggal' => $this->tanggal,
                    'kategori' => $this->kategori,
                ]);

                foreach ($this->details as $detail) {
                    $pesanan->details()->create($detail);
                }

                session()->flash('message', 'Pesanan berhasil disimpan!');
            }

            $this->resetForm();
            $this->dispatch('refreshTable');
            $this->dispatch('focus-tanggal');
        }

        private function resetForm()
        {
            $this->selectedId = null; // Reset ID
            $this->tanggal = date('Y-m-d');
            $this->kategori = 'UMUM';
            $this->details = [
                ['obat_id' => '', 'qty' => 1, 'harga' => 0, 'jumlah' => 0]
            ];
            $this->no_sp = $this->generateNoSp();
        }

        public function render()
        {
            return view('livewire.pesanan-form');
        }

        protected $listeners = [
            'refreshKodepesanan' => 'refreshKodepesanan',
            'edit-pesanan' => 'edit',
        ];
        public function edit($id)
        {
            $this->selectedId = $id;
            $pesanan = Pesanan::with('details.obat')->findOrFail($id);

            $this->no_sp = $pesanan->no_sp;
            $this->tanggal = $pesanan->tanggal;
            $this->kategori = $pesanan->kategori;

            $this->details = $pesanan->details->map(function ($detail) {
                return [
                    'obat_id' => $detail->obat_id,
                    'nama_obat' => $detail->obat->nama_obat ?? '',
                    'qty' => $detail->qty,
                    'harga' => $detail->harga,
                    'isi' => $detail->obat->isi ?? '',
                    'satuan' => $detail->obat->satuan->nama_satuan ?? '',
                    'jumlah' => $detail->jumlah,
                ];
            })->toArray();

            $this->dispatch('focus-tanggal');
        }

        public $selectedId = null;

        public function refreshKodepesanan()
        {
            $this->no_sp = $this->generateNoSp();
        }
        public $obatList = [];
        public $obatSearch = [];          // Hasil pencarian per index row
        public $showObatDropdown = [];    // Status dropdown per index row

        public function searchObat($index, $query)
        {
            $this->obatSearch[$index] = Obat::where('nama_obat', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            $this->showObatDropdown[$index] = true;
        }

        public function selectObat($index, $id)
        {
            $selected = \App\Models\Obat::with('satuan')->find($id);

            if ($selected) {
                $this->details[$index]['obat_id'] = $selected->id;
                $this->details[$index]['nama_obat'] = $selected->nama_obat;
                $this->details[$index]['harga'] = $selected->harga_jual ?? 0;
                $this->details[$index]['isi'] = $selected->isi_obat ?? 0;
                $this->details[$index]['satuan'] = $selected->satuan?->nama_satuan ?? '';

                $this->showObatDropdown[$index] = false;
            }
        }

        public function selectFirstObat($index)
        {
            if (!empty($this->obatSearch[$index])) {
                $firstObat = $this->obatSearch[$index]->first();
                $this->selectObat($index, $firstObat->id);
            }
        }

        public $highlightedIndex = []; // simpan index highlight per row

        public function resetHighlight($index)
        {
            $this->highlightedIndex[$index] = 0;
        }

        public function incrementHighlight($index)
        {
            if (!isset($this->highlightedIndex[$index])) {
                $this->highlightedIndex[$index] = 0;
            }

            $count = isset($this->obatSearch[$index]) ? $this->obatSearch[$index]->count() : 0;

            if ($count > 0) {
                $this->highlightedIndex[$index] = ($this->highlightedIndex[$index] + 1) % $count;
            }
        }

        public function decrementHighlight($index)
        {
            if (!isset($this->highlightedIndex[$index])) {
                $this->highlightedIndex[$index] = 0;
            }

            $count = isset($this->obatSearch[$index]) ? $this->obatSearch[$index]->count() : 0;

            if ($count > 0) {
                $this->highlightedIndex[$index] = ($this->highlightedIndex[$index] - 1 + $count) % $count;
            }
        }

        public function selectHighlightedObat($index)
        {
            // Default index ke 0 kalau belum ada
            if (!isset($this->highlightedIndex[$index])) {
                $this->highlightedIndex[$index] = 0;
            }

            if (!empty($this->obatSearch[$index])) {
                $selected = $this->obatSearch[$index][$this->highlightedIndex[$index]];

                // Isi detail obat
                $this->details[$index]['obat_id'] = $selected->id;
                $this->details[$index]['nama_obat'] = $selected->nama_obat;
                $this->details[$index]['harga'] = $selected->harga_jual ?? 0;
                $this->details[$index]['isi'] = $selected->isi_obat ?? 0;
                $this->details[$index]['satuan'] = $selected->satuan?->nama_satuan ?? '';

                // Hitung jumlah awal (qty * harga)
                $qty = $this->details[$index]['qty'] ?? 1; // Default 1 kalau kosong
                $this->details[$index]['jumlah'] = $qty * ($this->details[$index]['harga'] ?? 0);

                // Tutup dropdown
                $this->showObatDropdown[$index] = false;
            }
        }
    }
