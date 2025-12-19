<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin', 'ketua_tim', 'anggota'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $permissions = [
            // PROJECT
            'view projects',
            'create projects',
            'edit projects',
            'delete projects',

            // TASKS
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'assign tasks',

            // REPORTS
            'view reports',
            'export reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::where('name', 'admin')->first();
        $ketua = Role::where('name', 'ketua_tim')->first();
        $anggota = Role::where('name', 'anggota')->first();

        if ($admin) {
            $admin->givePermissionTo(Permission::all());
        }

        if ($ketua) {
            $ketua->givePermissionTo([
                'view projects',
                'edit projects',

                'view tasks',
                'create tasks',
                'edit tasks',
                'assign tasks',

                'view reports',
            ]);
        }

        if ($anggota) {
            $anggota->givePermissionTo([
                'view project'
            ]);
        }

        $users = User::all();

        foreach ($users as $user) {
            if ($user->role) {
                $user->syncRoles($user->role);
            }
        }
    }
}
