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
     * Auto import 'Siap Musnah' to table daftar_pemusnahan
     */
    public function importSiapMusnah()
    {
        $retensiList = Retensi::where('status_retensi', 'Siap Musnah')->get();

        foreach ($retensiList as $item) {
            Pemusnahan::firstOrCreate(
                ['no_rm' => $item->no_rm],
                [
                    'tanggal_retensi' => Carbon::now(),
                    'status' => 'menunggu_persetujuan'
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

        $data = Pemusnahan::with('pasien')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Approval Action By Role
     */
    public function approve(Request $request, $id)
    {
        $pemusnahan = Pemusnahan::findOrFail($id);
        $user = auth()->user();

        try {
            DB::beginTransaction();
            if ($user->role === 'Kepala_RM') { // Misal role
                $pemusnahan->update([
                    'approved_kepala_rm' => $user->id,
                    'tanggal_approval_rm' => now(),
                    'status' => 'disetujui' // Asal 1 approval untuk contoh sederhana
                ]);
                ActivityLogService::log('Pemusnahan', 'Approve Kepala RM', "Approve RM: {$pemusnahan->no_rm}");
            } 
            elseif ($user->role === 'Administrator' || $user->role === 'Direktur') {
                $pemusnahan->update([
                    'approved_direktur' => $user->id,
                    'tanggal_approval_direktur' => now(),
                    'status' => 'disetujui'
                ]);
                ActivityLogService::log('Pemusnahan', 'Approve Direktur', "Approve RM: {$pemusnahan->no_rm}");
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Status disetujui.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Final Execute Musnahkan (Membangkitkan Berita Acara & Delete soft/hard)
     */
    public function musnahkan($id)
    {
        $pemusnahan = Pemusnahan::where('status', 'disetujui')->findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            // Mark deleted
            $pemusnahan->update(['status' => 'dimusnahkan']);
            
            // Update Retensi Status
            Retensi::where('no_rm', $pemusnahan->no_rm)->update(['status_retensi' => 'Dimusnahkan']);

            // Insert Berita Acara
            \App\Models\BeritaAcara::create([
                'id_pemusnahan' => $pemusnahan->id,
                'nomor_berita_acara' => 'BA/' . Carbon::now()->format('Y/m/d') . '/' . $pemusnahan->id,
                'tanggal_pemusnahan' => now(),
            ]);

            ActivityLogService::log('Pemusnahan', 'Eksekusi Musnah', "User memusnahkan RM: {$pemusnahan->no_rm}");

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dokumen berhasil dieksekusi pemusnahan & Berita Acara tercipta.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
