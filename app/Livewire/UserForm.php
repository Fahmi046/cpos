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
    public $role = 'user';
    public $aktif = true;

    protected $rules = [
        'name'     => 'required|string|max:100',
        'username' => 'required|string|max:50|unique:users,username',
        'email'    => 'required|email|unique:users,email',
        'password' => 'nullable|string|min:6',
        'role'     => 'required|string|in:admin,user,manager',
        'aktif'    => 'boolean',
    ];

    protected $listeners = [
        'editUser' => 'edit',
        'refreshForm' => '$refresh',
    ];

    public function render()
    {
        return view('livewire.user-form');
    }

    public function save()
    {
        $validatedData = $this->validate();

        if (empty($validatedData['password'])) {
            unset($validatedData['password']);
        } else {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        User::updateOrCreate(
            ['id' => $this->user_id],
            $validatedData
        );

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
