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

        // Filter status (dummy, karena tidak ada column ini di mysql legacy)
        if ($request->has('status') && $request->status) {
            if ($request->status == 'Nonaktif') {
                $query->whereRaw('1 = 0');
            }
        }

        // Filter kategori
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kelompok', $request->kategori);
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
            'nama_kasus' => 'required|string',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string',
            'masa_retensi_aktif' => 'required|integer|min:1',
            'masa_retensi_inaktif' => 'required|integer|min:1',
        ]);

        $kasus = Kasus::create([
            'jenis_kasus' => $validated['nama_kasus'],
            'keterangan' => $validated['deskripsi'],
            'kelompok' => $validated['kategori'],
            'masa_aktif_rj' => $validated['masa_retensi_aktif'],
            'masa_inaktif_rj' => $validated['masa_retensi_inaktif'],
            'masa_aktif_ri' => $validated['masa_retensi_aktif'],
            'masa_inaktif_ri' => $validated['masa_retensi_inaktif'],
        ]);

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
            'nama_kasus' => 'required|string',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string',
            'masa_retensi_aktif' => 'required|integer|min:1',
            'masa_retensi_inaktif' => 'required|integer|min:1',
        ]);

        $kasus->update([
            'jenis_kasus' => $validated['nama_kasus'],
            'keterangan' => $validated['deskripsi'],
            'kelompok' => $validated['kategori'],
            'masa_aktif_rj' => $validated['masa_retensi_aktif'],
            'masa_inaktif_rj' => $validated['masa_retensi_inaktif'],
            'masa_aktif_ri' => $validated['masa_retensi_aktif'],
            'masa_inaktif_ri' => $validated['masa_retensi_inaktif'],
        ]);

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
        $kategori = Kasus::distinct('kelompok')->whereNotNull('kelompok')->pluck('kelompok');
        return response()->json($kategori);
    }
}
