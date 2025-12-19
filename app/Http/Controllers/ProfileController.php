<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:view profile')->only(['show']);
        $this->middleware('permission:edit profile')->only(['edit', 'update', 'updatePassword']);
    }

    public function show()
    {
        $user = Auth::user();
        $userRole = $user->getRoleNames()->first() ?? 'anggota_tim';

        return view('profile.show', compact('user', 'userRole'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $userToEdit = User::findOrFail($user->id);
        $this->authorize('update profile', $userToEdit);


        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'github' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'role' => 'nullable|exists:roles,name',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                Storage::delete('public/avatars/' . $user->avatar);
            }

            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/avatars', $filename);
            $validated['avatar'] = $filename;
        }

        $user->update($validated);

        if ($request->filled('role')) {
            $user->syncRoles($request->role);

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
        }

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama tidak cocok.')->with('openModal', true);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diubah.')->with('openModal', true);
    }
}
