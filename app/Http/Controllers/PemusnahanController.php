<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemusnahan;
use App\Models\Retensi;
use App\Models\Pasien;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PemusnahanController extends Controller
{
    /**
     * Auto import 'Siap Dimusnahkan' to table daftar_pemusnahan
     */
    public function importSiapMusnah()
    {
        $retensiList = Retensi::where('status', 'Siap Dimusnahkan')->get();

        foreach ($retensiList as $item) {
            Pemusnahan::firstOrCreate(
                ['no_rm' => $item->no_rm],
                [
                    'tanggal_retensi' => Carbon::now(),
                    'status' => 'menunggu_eksekusi'
                ]
            );
        }
    }

    /**
     * Get List of Pemusnahan
     */
    public function index()
    {
        $this->importSiapMusnah();

        $data = Pemusnahan::with('pasien')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'no_rm' => $item->no_rm,
                'nama_pasien' => $item->pasien?->nama_pasien ?? '-',
                'tanggal_retensi' => $item->tanggal_retensi,
                'status' => $item->status,
                'approved_kepala_rm' => $item->approved_kepala_rm,
                'tanggal_approval_rm' => $item->tanggal_approval_rm,
                'approved_direktur' => $item->approved_direktur,
                'tanggal_approval_direktur' => $item->tanggal_approval_direktur,
                'tanggal_pemusnahan' => $item->tanggal_pemusnahan,
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Stub for approveKepalaRM (not used in simplified flow)
     */
    public function approveKepalaRM($id)
    {
        return response()->json(['success' => true, 'message' => 'Persetujuan Kepala RM disimulasikan.']);
    }

    /**
     * Stub for approveDirektur (not used in simplified flow)
     */
    public function approveDirektur($id)
    {
        return response()->json(['success' => true, 'message' => 'Persetujuan Direktur disimulasikan.']);
    }

    /**
     * Stub for reject (not used in simplified flow)
     */
    public function reject($id)
    {
        return response()->json(['success' => true, 'message' => 'Penolakan disimulasikan.']);
    }

    /**
     * Direct Execute Musnahkan (Membangkitkan Berita Acara & Update Status)
     */
    public function musnahkan($id)
    {
        $pemusnahan = Pemusnahan::findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            // Mark as destroyed
            $pemusnahan->update([
                'status' => 'dimusnahkan',
                'tanggal_pemusnahan' => now(),
            ]);
            
            // Update Retensi Status
            Retensi::where('no_rm', $pemusnahan->no_rm)->update(['status' => 'Dimusnahkan']);

            // Insert Berita Acara if not exists
            \App\Models\BeritaAcara::firstOrCreate(
                ['id_pemusnahan' => $pemusnahan->id],
                [
                    'nomor_berita_acara' => 'BA/' . Carbon::now()->format('Y/m/d') . '/' . $pemusnahan->id,
                    'tanggal_pemusnahan' => now(),
                ]
            );

            ActivityLogService::log('Pemusnahan', 'Eksekusi Musnah', "User memusnahkan RM: {$pemusnahan->no_rm}");

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dokumen berhasil dieksekusi pemusnahan & Berita Acara tercipta.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate Berita Acara (Returns a mock PDF path for frontend download)
     */
    public function generateBeritaAcara($id)
    {
        $pemusnahan = Pemusnahan::findOrFail($id);
        
        // Ensure BeritaAcara exists in DB
        $ba = \App\Models\BeritaAcara::firstOrCreate(
            ['id_pemusnahan' => $pemusnahan->id],
            [
                'nomor_berita_acara' => 'BA/' . Carbon::now()->format('Y/m/d') . '/' . $pemusnahan->id,
                'tanggal_pemusnahan' => now(),
            ]
        );

        // Ensure storage directory for berita-acara exists
        $publicDir = public_path('storage');
        if (!file_exists($publicDir)) {
            mkdir($publicDir, 0755, true);
        }

        $dummyFile = $publicDir . '/berita-acara-dummy.pdf';
        if (!file_exists($dummyFile)) {
            file_put_contents($dummyFile, "DUMMY BERITA ACARA PDF CONTENT\nNomor: " . $ba->nomor_berita_acara);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berita acara berhasil dibuat',
            'file_path' => '/storage/berita-acara-dummy.pdf'
        ]);
    }
}
