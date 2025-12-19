<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SyncUserRolesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereNotNull('role')->get();

        foreach ($users as $user) {
            $role = Role::firstOrCreate(['name' => $user->role]);

            $user->syncRoles([$role->name]);

            echo "✅ {$user->name} ({$user->email}) disinkronkan sebagai {$role->name}\n";
        }

        echo "🎯 Semua user berhasil disinkronkan!\n";
    }
}
