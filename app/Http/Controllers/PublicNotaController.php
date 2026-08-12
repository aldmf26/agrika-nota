<?php

namespace App\Http\Controllers;

use App\Models\Nota;

class PublicNotaController extends Controller
{
    /**
     * Tampilkan detail nota melalui token publik acak.
     */
    public function show(string $token)
    {
        $nota = Nota::with(['user', 'divisi', 'approver', 'items', 'attachments'])
            ->where('public_token', $token)
            ->firstOrFail();

        return view('nota.public-show', compact('nota'));
    }
}
