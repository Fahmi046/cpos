<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class UserTable extends Component
{
    public $users;

    protected $listeners = ['refreshUserTable' => '$refresh'];

    public function render()
    {
        // Ambil semua user terbaru
        $this->users = User::latest()->get();
        return view('livewire.user-table');
    }

    public function edit($id)
    {
        // Dispatch event untuk edit
        $this->dispatch('editUser', $id);
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();

        // Flash message jika perlu
        session()->flash('message', 'User berhasil dihapus');

        // Refresh tabel
        $this->dispatch('refreshUserTable');
    }
}
