<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserForm extends Component
{
    public $user_id;
    public $name;
    public $username;
    public $email;
    public $password;
    public $role = 'outlet'; // default role
    public $aktif = true;

    protected $listeners = [
        'editUser' => 'edit',
        'refreshForm' => '$refresh',
    ];

    // Rules dinamis
    protected function rules()
    {
        return [
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $this->user_id,
            'email'    => 'required|email|unique:users,email,' . $this->user_id,
            'password' => 'nullable|string|min:6',
            'role'     => 'required|string|in:admin,gudang,outlet',
            'aktif'    => 'sometimes|boolean',
        ];
    }

    public function render()
    {
        return view('livewire.user-form');
    }

    public function save()
    {
        $validatedData = $this->validate();

        // Jika role outlet, wajib punya outlet_id
        if ($this->role === 'outlet') {
            if (!$this->outlet_id) {
                $this->addError('outlet_id', 'Outlet harus dipilih untuk role Outlet.');
                return;
            }
            $validatedData['outlet_id'] = $this->outlet_id;
        } else {
            // Untuk admin/gudang, kosongkan outlet_id
            $validatedData['outlet_id'] = null;
        }

        // Proses password
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // Simpan (update atau create)
        $user = User::find($this->user_id);
        if ($user) {
            $user->update($validatedData);
        } else {
            User::create($validatedData);
        }

        // Event & reset form
        $this->dispatch('user-saved');
        $this->resetForm();
        $this->role = 'outlet';
        $this->aktif = true;
        $this->dispatch('focus-name');
        $this->dispatch('refreshUserTable');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->aktif = $user->aktif;
        $this->password = null;
    }

    public function resetForm(): void
    {
        $this->reset([
            'user_id',
            'name',
            'username',
            'email',
            'password',
            'role',
            'aktif',
            'searchOutlet',
            'outlet_id',
            'outlet_nama',
            'outletResults',
            'highlightIndexOutlet',
        ]);
    }


    //nama outlet

    public $searchOutlet = '';
    public $outletResults = [];
    public $highlightIndexOutlet = 0;
    public $outlet_id;
    public $outlet_nama;

    // Search outlet untuk autocomplete
    public function updatedSearchOutlet()
    {
        if (strlen($this->searchOutlet) > 1) {
            $this->outletResults = \App\Models\Outlet::where('nama_outlet', 'like', '%' . $this->searchOutlet . '%')
                ->limit(10)
                ->get()
                ->toArray();
            $this->highlightIndexOutlet = 0;
        } else {
            $this->outletResults = [];
            $this->highlightIndexOutlet = 0;
        }
    }

    public function incrementHighlightOutlet()
    {
        if (count($this->outletResults) === 0) return;
        $this->highlightIndexOutlet++;
        if ($this->highlightIndexOutlet >= count($this->outletResults)) {
            $this->highlightIndexOutlet = 0;
        }
    }

    public function decrementHighlightOutlet()
    {
        if (count($this->outletResults) === 0) return;
        $this->highlightIndexOutlet--;
        if ($this->highlightIndexOutlet < 0) {
            $this->highlightIndexOutlet = count($this->outletResults) - 1;
        }
    }

    public function selectHighlightedOutlet()
    {
        if (isset($this->outletResults[$this->highlightIndexOutlet])) {
            $this->selectOutlet($this->outletResults[$this->highlightIndexOutlet]['id']);
        }
    }

    public function selectOutlet($id)
    {
        $outlet = \App\Models\Outlet::find($id);
        if ($outlet) {
            $this->outlet_id = $outlet->id;
            $this->outlet_nama = $outlet->nama_outlet;
            $this->searchOutlet = $outlet->nama_outlet;
            $this->outletResults = [];
            $this->highlightIndexOutlet = 0;
        }
    }
}
