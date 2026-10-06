<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('orders')
            ->latest()
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        if ((int) $user->getKey() === (int) $request->user()->getKey()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat mengubah role akun sendiri.');
        }

        if ($user->role === 'admin') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Role akun admin tidak dapat diubah di sini.');
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:user,eo'],
        ]);

        $user->role = $validated['role'];
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Role pengguna berhasil diperbarui.');
    }
}
