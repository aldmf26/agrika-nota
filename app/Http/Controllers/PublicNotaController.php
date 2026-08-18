<?php

namespace App\Http\Controllers;

use App\Models\Nota;

class PublicNotaController extends Controller
{
    /**
     * Tampilkan detail nota melalui token publik acak.
     */
    public function show(string $token, string $verification = 'approval')
    {
        $nota = Nota::with(['user', 'approver'])
            ->where('public_token', $token)
            ->firstOrFail();

        abort_unless(in_array($verification, ['creator', 'approval'], true), 404);

        return view('nota.public-show', compact('nota', 'verification'));
    }
}
