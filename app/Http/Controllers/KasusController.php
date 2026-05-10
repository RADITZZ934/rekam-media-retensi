<?php

namespace App\Http\Controllers;

use App\Models\Kasus;
use Illuminate\Http\Request;

class KasusController extends Controller
{
    /**
     * Get daftar kasus dengan filter dan search
     */
    public function index(Request $request)
    {
        $query = Kasus::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $kasus = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($kasus);
    }

    /**
     * Get detail kasus
     */
    public function show($id)
    {
        $kasus = Kasus::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $kasus,
        ]);
    }

    /**
     * Store kasus baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kasus' => 'required|string|unique:kasus_master,kode_kasus',
            'nama_kasus' => 'required|string',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string',
            'masa_retensi_aktif' => 'required|integer|min:1',
            'masa_retensi_inaktif' => 'required|integer|min:1',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        $kasus = Kasus::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kasus berhasil ditambahkan',
            'data' => $kasus,
        ]);
    }

    /**
     * Update kasus
     */
    public function update(Request $request, $id)
    {
        $kasus = Kasus::findOrFail($id);

        $validated = $request->validate([
            'kode_kasus' => 'required|string|unique:kasus_master,kode_kasus,' . $id,
            'nama_kasus' => 'required|string',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string',
            'masa_retensi_aktif' => 'required|integer|min:1',
            'masa_retensi_inaktif' => 'required|integer|min:1',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        $kasus->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kasus berhasil diperbarui',
            'data' => $kasus,
        ]);
    }

    /**
     * Delete kasus
     */
    public function destroy($id)
    {
        $kasus = Kasus::findOrFail($id);
        $kasus->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kasus berhasil dihapus',
        ]);
    }

    /**
     * Get list kategori yang unik
     */
    public function getKategori()
    {
        $kategori = Kasus::distinct('kategori')->pluck('kategori');
        return response()->json($kategori);
    }
}
