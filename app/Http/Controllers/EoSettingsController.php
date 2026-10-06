<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EoSettingsController extends Controller
{
    public function edit(Request $request)
    {
        return view('eo.settings.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->fill($validated)->save();

        return redirect()
            ->route('eo.settings.edit')
            ->with('success', 'Nama creator berhasil diperbarui.');
    }
}
