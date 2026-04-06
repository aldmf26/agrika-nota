<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Http\Request;

class PublicNotaController extends Controller
{
    /**
     * Tampilkan detail nota untuk publik via nomor_nota.
     */
    public function show($nomor_nota)
    {
        $nota = Nota::with(['user', 'divisi', 'approver', 'items', 'attachments'])
            ->where('nomor_nota', $nomor_nota)
            ->firstOrFail();

        return view('nota.public-show', compact('nota'));
    }
}
