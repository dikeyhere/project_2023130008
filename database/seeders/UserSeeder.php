<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Ketua Tim',
            'email' => 'ketua@example.com',
            'password' => Hash::make('password'),
            'role' => 'ketua_tim',
        ]);

        User::create([
            'name' => 'Anggota Tim',
            'email' => 'anggota@example.com',
            'password' => Hash::make('password'),
            'role' => 'anggota_tim',
        ]);
    }
}
