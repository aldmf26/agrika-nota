<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    public function index()
    {
        $divisis = Divisi::all();
        return view('admin.divisi.index', compact('divisis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.nama' => 'required|unique:divisis,nama',
            'items.*.kode' => 'required|max:10|unique:divisis,kode',
            'items.*.deskripsi' => 'nullable',
        ]);

        foreach ($validated['items'] as $item) {
            Divisi::create([
                'nama' => $item['nama'],
                'kode' => strtoupper($item['kode']),
                'deskripsi' => $item['deskripsi'],
                'aktif' => true,
            ]);
        }

        return redirect()->route('admin.divisi.index')->with('success', count($validated['items']) . ' divisi berhasil dibuat.');
    }

    public function edit(Divisi $divisi)
    {
        return view('admin.divisi.edit', compact('divisi'));
    }

    public function update(Request $request, Divisi $divisi)
    {
        $validated = $request->validate([
            'nama' => 'required|unique:divisis,nama,' . $divisi->id,
            'kode' => 'required|max:10|unique:divisis,kode,' . $divisi->id,
            'deskripsi' => 'nullable',
            'aktif' => 'boolean',
        ]);

        $divisi->update($validated);

        return redirect()->route('admin.divisi.index')->with('success', 'Divisi berhasil diupdate.');
    }

    public function destroy(Divisi $divisi)
    {
        // Check if has related notas
        if ($divisi->notas()->exists()) {
            return back()->with('error', 'Gagal: Divisi ini masih digunakan di beberapa nota.');
        }

        $divisi->delete();

        return redirect()->route('admin.divisi.index')->with('success', 'Divisi berhasil dihapus.');
    }
}
