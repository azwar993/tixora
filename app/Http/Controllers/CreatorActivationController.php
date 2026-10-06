<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CreatorActivationController extends Controller
{
    public function activate(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->role === 'eo') {
            return redirect()->route('eo.dashboard');
        }

        abort_unless($user->role === 'user', 403);

        $user->role = 'eo';
        $user->save();

        return redirect()
            ->route('eo.dashboard')
            ->with('success', 'Selamat! Akun kamu sekarang aktif sebagai Event Creator.');
    }
}
