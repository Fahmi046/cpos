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

        // Hanya simpan password jika diisi
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // Jika edit, update hanya field yang diubah
        $user = User::find($this->user_id);
        if ($user) {
            $user->update($validatedData);
        } else {
            User::create($validatedData);
        }

        $this->dispatch('user-saved');
        $this->resetForm();
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
        ]);
    }
}
