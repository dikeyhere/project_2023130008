<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // Dashboard & Reports
            'view dashboard',
            'view reports',
            'export reports',
            'export reports pdf',
            'export reports excel',

            // Projects
            'view projects',
            'view own projects',  
            'view project detail',
            'create projects',
            'edit projects',
            'delete projects',

            // Tasks
            'view tasks',
            'view assigned tasks',  
            'create tasks',
            'edit tasks',
            'delete tasks',
            'upload task file',
            'update task progress',
            'submit task',

            // Profile
            'view profile',
            'update profile',
            'delete profile',
            'change password',

            // Financial / Expense
            'view financial',
            'submit expense',
            'approve expense',
            'reject expense',

            // Permission Management
            'manage permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $ketua = Role::firstOrCreate(['name' => 'ketua_tim']);
        $anggota = Role::firstOrCreate(['name' => 'anggota_tim']);

        /*
        |--------------------------------------------------------------------------
        | ROLE → PERMISSION
        |--------------------------------------------------------------------------
        */
        $admin->syncPermissions(Permission::all());

        $ketua->syncPermissions([
            'view dashboard',

            'view projects',
            'view own projects',
            'view project detail',
            'create projects',
            'edit projects',

            'view tasks',
            'create tasks',
            'edit tasks',

            'view reports',
            'export reports',
            'export reports pdf',
            'export reports excel',

            'view financial',
            'submit expense',
            'approve expense',
            'reject expense',

            'view profile',
            'edit profile',
            'change password',
        ]);

        $anggota->syncPermissions([
            'view dashboard',

            'view projects',
            'view assigned tasks',
            'view project detail',
            'view tasks',
            'upload task file',
            'update task progress',
            'submit task',

            'submit expense',

            'view profile',
            'edit profile',
            'change password',
        ]);
    }
}
