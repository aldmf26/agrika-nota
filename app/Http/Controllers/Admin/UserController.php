<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array($request->integer('per_page'), [10, 25, 50, 100], true)
            ? $request->integer('per_page')
            : 10;
        $search = trim((string) $request->input('search'));

        $users = User::with(['roles', 'divisis'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('roles', fn ($roleQuery) => $roleQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
        $roles = Role::all();
        $divisis = Divisi::orderBy('nama')->get();

        return view('admin.user.index', compact('users', 'roles', 'divisis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
            'all_divisi' => 'nullable|boolean',
            'divisi_ids' => 'nullable|array',
            'divisi_ids.*' => 'exists:divisis,id',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'all_divisi' => $request->has('all_divisi'),
            ]);
        } catch (UniqueConstraintViolationException) {
            return back()->withInput()->with('error', 'Email sudah digunakan oleh user lain.');
        }

        $user->assignRole($validated['role']);
        
        if (! $user->all_divisi && $request->has('divisi_ids')) {
            $user->divisis()->sync($request->input('divisi_ids'));
        } else {
            $user->divisis()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name',
            'all_divisi' => 'nullable|boolean',
            'divisi_ids' => 'nullable|array',
            'divisi_ids.*' => 'exists:divisis,id',
        ]);

        try {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'all_divisi' => $request->has('all_divisi'),
            ]);

            if (! empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }
        } catch (UniqueConstraintViolationException) {
            return back()->withInput()->with('error', 'Email sudah digunakan oleh user lain.');
        }

        $user->syncRoles($validated['role']);

        if (! $user->all_divisi && $request->has('divisi_ids')) {
            $user->divisis()->sync($request->input('divisi_ids'));
        } else {
            $user->divisis()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Gagal: Anda tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}

