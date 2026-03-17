<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Kunjungan;
use App\Models\Retensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PasienController extends Controller
{
    /**
     * Get daftar pasien dengan filter dan search
     */
    public function index(Request $request)
    {
        $query = Pasien::with(['kunjunganTerakhir', 'retensi']);

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter status RM
        if ($request->has('status_rm') && $request->status_rm) {
            $query->statusRm($request->status_rm);
        }

        // Filter status Retensi
        if ($request->has('status_retensi') && $request->status_retensi) {
            $query->whereHas('retensi', function ($q) {
                $q->where('status_retensi', request('status_retensi'));
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $pasien = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Transform data untuk frontend
        $pasien->getCollection()->transform(function ($item) {
            return $this->formatPasienData($item);
        });

        return response()->json($pasien);
    }

    /**
     * Get detail pasien
     */
    public function show($no_rm)
    {
        $pasien = Pasien::with(['kunjungan', 'retensi'])->findOrFail($no_rm);
        
        return response()->json([
            'success' => true,
            'data' => $this->formatPasienData($pasien),
            'kunjungan' => $pasien->kunjungan,
            'retensi' => $pasien->retensi,
        ]);
    }

    /**
     * Store pasien baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_rm' => 'required|string|unique:pasien,no_rm',
            'nama_pasien' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string',
            'status_rm' => 'required|in:Aktif,Inaktif',
        ]);

        $pasien = Pasien::create($validated);

        // Create default retensi record
        Retensi::create([
            'no_rm' => $pasien->no_rm,
            'status_retensi' => 'Aktif',
            'tanggal_mulai_retensi' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pasien berhasil ditambahkan',
            'data' => $this->formatPasienData($pasien->fresh(['retensi'])),
        ]);
    }

    /**
     * Update pasien
     */
    public function update(Request $request, $no_rm)
    {
        $pasien = Pasien::findOrFail($no_rm);

        $validated = $request->validate([
            'nama_pasien' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string',
            'status_rm' => 'required|in:Aktif,Inaktif',
        ]);

        $pasien->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pasien berhasil diperbarui',
            'data' => $this->formatPasienData($pasien->fresh(['kunjunganTerakhir', 'retensi'])),
        ]);
    }

    /**
     * Delete pasien
     */
    public function destroy($no_rm)
    {
        $pasien = Pasien::findOrFail($no_rm);
        $pasien->delete(); // Will cascade delete kunjungan dan retensi

        return response()->json([
            'success' => true,
            'message' => 'Pasien berhasil dihapus',
        ]);
    }

    /**
     * Format data pasien untuk response
     */
    private function formatPasienData($pasien)
    {
        $kunjunganTerakhir = $pasien->kunjunganTerakhir;
        $tglKunjunganTerakhir = $kunjunganTerakhir?->tanggal_keluar;

        return [
            'no_rm' => $pasien->no_rm,
            'nama_pasien' => $pasien->nama_pasien,
            'jenis_kelamin' => $pasien->jenis_kelamin,
            'tanggal_lahir' => $pasien->tanggal_lahir?->format('d/m/Y'),
            'tempat_lahir' => $pasien->tempat_lahir,
            'alamat' => $pasien->alamat,
            'no_telepon' => $pasien->no_telepon,
            'status_rm' => $pasien->status_rm,
            'tgl_kunjungan_terakhir' => $tglKunjunganTerakhir?->format('d/m/Y'),
            'status_retensi' => $pasien->retensi?->status_retensi ?? 'Belum di-set',
            'created_at' => $pasien->created_at,
        ];
    }
}
