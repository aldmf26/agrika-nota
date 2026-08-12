<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DivisiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array($request->integer('per_page'), [10, 25, 50, 100], true)
            ? $request->integer('per_page')
            : 10;
        $search = trim((string) $request->input('search'));

        $divisis = Divisi::withCount('notas')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.divisi.index', compact('divisis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.nama' => 'required|string|max:255|distinct:ignore_case|unique:divisis,nama',
            'items.*.kode' => 'required|string|max:10|distinct:ignore_case|unique:divisis,kode',
            'items.*.deskripsi' => 'nullable',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['items'] as $item) {
                    Divisi::create([
                        'nama' => $item['nama'],
                        'kode' => strtoupper($item['kode']),
                        'deskripsi' => $item['deskripsi'] ?? null,
                        'aktif' => true,
                    ]);
                }
            });
        } catch (UniqueConstraintViolationException) {
            return back()->withInput()->with('error', 'Nama atau kode divisi sudah digunakan.');
        }

        return redirect()->route('admin.divisi.index')->with('success', count($validated['items']).' divisi berhasil dibuat.');
    }

    public function edit(Divisi $divisi)
    {
        return view('admin.divisi.edit', compact('divisi'));
    }

    public function update(Request $request, Divisi $divisi)
    {
        $validated = $request->validate([
            'nama' => 'required|unique:divisis,nama,'.$divisi->id,
            'kode' => 'required|max:10|unique:divisis,kode,'.$divisi->id,
            'deskripsi' => 'nullable',
            'aktif' => 'boolean',
        ]);

        try {
            $divisi->update($validated);
        } catch (UniqueConstraintViolationException) {
            return back()->withInput()->with('error', 'Nama atau kode divisi sudah digunakan.');
        }

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
