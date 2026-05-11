<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Kunjungan;
use App\Models\Retensi;
use App\Models\Kasus;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PasienController extends Controller
{
    /**
     * Get daftar pasien dengan filter dan search
     */
    public function index(Request $request)
    {
        $query = Pasien::with(['kunjunganTerakhir', 'retensi', 'kasus']);

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
        $pasien = Pasien::with(['kunjungan', 'retensi', 'kasus'])->findOrFail($no_rm);
        
        return response()->json([
            'success' => true,
            'data' => $this->formatPasienData($pasien),
            'kunjungan' => $pasien->kunjungan,
            'retensi' => $pasien->retensi,
            'kasus' => $pasien->kasus,
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
            'kasus_id' => 'nullable|exists:kasus_master,id',
        ]);

        $pasien = Pasien::create($validated);

        // Get Kasus if assigned
        $kasus = $pasien->kasus_id ? Kasus::find($pasien->kasus_id) : null;

        // Create kunjungan awal
        $kunjungan = Kunjungan::create([
            'no_rm' => $pasien->no_rm,
            'tanggal_masuk' => Carbon::now(),
            'tanggal_keluar' => Carbon::now(),
            'diagnosis' => 'Kunjungan awal',
        ]);

        // Calculate retensi dates berdasarkan Kasus
        $retensiData = $this->calculateRetensiDates($kasus, $kunjungan->tanggal_keluar);

        // Create retensi record dengan perhitungan otomatis
        Retensi::create([
            'no_rm' => $pasien->no_rm,
            'kasus_id' => $pasien->kasus_id,
            'status_retensi' => $retensiData['status'],
            'tanggal_mulai_retensi' => Carbon::now(),
            'tanggal_batas_aktif' => $retensiData['tanggal_batas_aktif'],
            'tanggal_batas_musnah' => $retensiData['tanggal_batas_musnah'],
            'keterangan' => $kasus ? "Retensi berdasarkan kasus: {$kasus->nama_kasus}" : 'Retensi default',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pasien berhasil ditambahkan',
            'data' => $this->formatPasienData($pasien->fresh(['kunjunganTerakhir', 'retensi', 'kasus'])),
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
            'kasus_id' => 'nullable|exists:kasus_master,id',
        ]);

        // Jika kasus berubah, recalculate retensi
        if ($pasien->kasus_id != $validated['kasus_id']) {
            $kasus = $validated['kasus_id'] ? Kasus::find($validated['kasus_id']) : null;
            $kunjunganTerakhir = $pasien->kunjunganTerakhir;
            $tglKunjungan = $kunjunganTerakhir?->tanggal_keluar ?? Carbon::now();

            $retensiData = $this->calculateRetensiDates($kasus, $tglKunjungan);

            // Update retensi record
            if ($pasien->retensi) {
                $pasien->retensi->update([
                    'kasus_id' => $validated['kasus_id'],
                    'tanggal_batas_aktif' => $retensiData['tanggal_batas_aktif'],
                    'tanggal_batas_musnah' => $retensiData['tanggal_batas_musnah'],
                    'keterangan' => $kasus ? "Retensi berdasarkan kasus: {$kasus->nama_kasus}" : 'Retensi default',
                ]);
            }
        }

        $pasien->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pasien berhasil diperbarui',
            'data' => $this->formatPasienData($pasien->fresh(['kunjunganTerakhir', 'retensi', 'kasus'])),
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
     * Calculate retensi dates dan status berdasarkan Kasus
     */
    private function calculateRetensiDates($kasus = null, $tanggalKunjungan = null)
    {
        $tanggalKunjungan = $tanggalKunjungan ?: Carbon::now();
        
        // Default retensi jika tidak ada Kasus
        $masaAktif = 5; // tahun
        $masaInaktif = 2; // tahun

        if ($kasus) {
            $masaAktif = $kasus->masa_retensi_aktif;
            $masaInaktif = $kasus->masa_retensi_inaktif;
        }

        // Calculate tanggal batas
        $tanggalBatasAktif = Carbon::parse($tanggalKunjungan)->addYears($masaAktif);
        $tanggalBatasMusnah = Carbon::parse($tanggalKunjungan)->addYears($masaAktif + $masaInaktif);

        // Tentukan status berdasarkan dates
        $now = Carbon::now();
        if ($now < $tanggalBatasAktif) {
            $status = 'Aktif';
        } elseif ($now < $tanggalBatasMusnah) {
            $status = 'Inaktif';
        } else {
            $status = 'Siap Musnah';
        }

        return [
            'status' => $status,
            'tanggal_batas_aktif' => $tanggalBatasAktif,
            'tanggal_batas_musnah' => $tanggalBatasMusnah,
        ];
    }

    /**
     * Format data pasien untuk response
     */
    private function formatPasienData($pasien)
    {
        $kunjunganTerakhir = $pasien->kunjunganTerakhir;
        $tglKunjunganTerakhir = $kunjunganTerakhir?->tanggal_keluar;

        // Recalculate retensi status dynamically
        $retensi = $pasien->retensi;
        $kasus = $pasien->kasus;
        
        $statusRetensi = 'Belum di-set';
        if ($retensi) {
            // Calculate status dynamically berdasarkan tanggal_batas
            $now = Carbon::now();
            if ($retensi->tanggal_batas_aktif && $now < $retensi->tanggal_batas_aktif) {
                $statusRetensi = 'Aktif';
            } elseif ($retensi->tanggal_batas_musnah && $now < $retensi->tanggal_batas_musnah) {
                $statusRetensi = 'Inaktif';
            } elseif ($retensi->tanggal_batas_musnah && $now >= $retensi->tanggal_batas_musnah) {
                $statusRetensi = 'Siap Musnah';
            } else {
                $statusRetensi = $retensi->status_retensi;
            }
        }

        return [
            'no_rm' => $pasien->no_rm,
            'nama_pasien' => $pasien->nama_pasien,
            'jenis_kelamin' => $pasien->jenis_kelamin,
            'tanggal_lahir' => $pasien->tanggal_lahir?->format('d/m/Y'),
            'tempat_lahir' => $pasien->tempat_lahir,
            'alamat' => $pasien->alamat,
            'no_telepon' => $pasien->no_telepon,
            'status_rm' => $pasien->status_rm,
            'kasus_id' => $pasien->kasus_id,
            'kasus_nama' => $kasus?->nama_kasus,
            'tgl_kunjungan_terakhir' => $tglKunjunganTerakhir?->format('d/m/Y'),
            'status_retensi' => $statusRetensi,
            'tgl_batas_aktif' => $retensi?->tanggal_batas_aktif?->format('d/m/Y'),
            'tgl_batas_musnah' => $retensi?->tanggal_batas_musnah?->format('d/m/Y'),
            'created_at' => $pasien->created_at,
        ];
    }
}
