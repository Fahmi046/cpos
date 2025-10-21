<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Cek dulu apakah user admin sudah ada, agar tidak duplikat
        if (!User::where('username', 'admin')->exists()) {
            User::create([
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@apotek.com',
                'password' => 'admin123',
                'role' => 'admin',
                'aktif' => true,
                'outlet_id' => null, // tidak terkait outlet
            ]);


            $this->command->info('Admin user berhasil dibuat.');
        } else {
            $this->command->info('Admin user sudah ada.');
        }
    }
}
