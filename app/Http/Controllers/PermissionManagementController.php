<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'permissions')->get();
        $roles = Role::all();
        $permissions = Permission::all();

        return view('access.permission', compact('users', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        if ($request->role) {
            $user->syncRoles([$request->role]);
        }

        if ($request->role === 'admin') {
            $user->syncPermissions(Permission::all());
        } elseif ($request->role === 'ketua_tim') {
            $user->syncPermissions([
                'view dashboard',
                'view projects',
                'view project detail',
                'create projects',
                'edit projects',
                'view tasks',
                'create tasks',
                'edit tasks',
                'view reports',
                'export reports',
                'view financial',
                'submit expense',
                'approve expense',
                'reject expense',
                'view profile',
                'edit profile',
            ]);
        } elseif ($request->role === 'anggota_tim') {
            $user->syncPermissions([
                'view dashboard',
                'view projects',
                'view project detail',
                'view tasks',
                'upload task file',
                'update task progress',
                'submit task',
                'submit expense',
                'view profile',
                'edit profile',
            ]);
        }

        return back()->with('success', 'Role dan permission berhasil diperbarui');
    }
}
