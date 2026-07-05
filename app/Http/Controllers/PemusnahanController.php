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
                'destroyed_by' => auth()->id() ?? 1,
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

    /**
     * Get unique years of destruction for filter
     */
    public function getTahunList()
    {
        $years = Pemusnahan::where('status', 'dimusnahkan')
            ->whereNotNull('tanggal_pemusnahan')
            ->selectRaw('YEAR(tanggal_pemusnahan) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return response()->json($years);
    }

    /**
     * Get Paginated and Filtered Report for destroyed items
     */
    public function report(Request $request)
    {
        $query = Pemusnahan::where('status', 'dimusnahkan')
            ->with(['pasien', 'beritaAcara', 'destroyedBy']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_rm', 'like', "%{$search}%")
                  ->orWhereHas('pasien', function ($qp) use ($search) {
                      $qp->where('nama_pasien', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->tahun) {
            $query->whereYear('tanggal_pemusnahan', $request->tahun);
        }

        $perPage = $request->input('per_page', 10);
        $paginated = $query->orderBy('tanggal_pemusnahan', 'desc')->paginate($perPage);

        $formattedData = collect($paginated->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'no_rm' => $item->no_rm,
                'nama_pasien' => $item->pasien?->nama_pasien ?? '-',
                'tanggal_retensi' => $item->tanggal_retensi ? Carbon::parse($item->tanggal_retensi)->format('Y-m-d') : '-',
                'tanggal_pemusnahan' => $item->tanggal_pemusnahan ? Carbon::parse($item->tanggal_pemusnahan)->format('Y-m-d H:i:s') : '-',
                'user_pemusnah' => $item->destroyedBy?->nama_lengkap ?? '-',
                'status' => 'Dimusnahkan'
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedData,
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage()
        ]);
    }

    /**
     * Export report to streamed CSV
     */
    public function exportReport(Request $request)
    {
        $query = Pemusnahan::where('status', 'dimusnahkan')
            ->with(['pasien', 'beritaAcara', 'destroyedBy']);

        ActivityLogService::log('Laporan', 'Export CSV Pemusnahan', "User melakukan ekspor CSV Laporan Pemusnahan");

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_rm', 'like', "%{$search}%")
                  ->orWhereHas('pasien', function ($qp) use ($search) {
                      $qp->where('nama_pasien', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->tahun) {
            $query->whereYear('tanggal_pemusnahan', $request->tahun);
        }

        $filename = 'laporan_pemusnahan_' . Carbon::now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($query) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write CSV headers
            fputcsv($file, [
                'No. RM',
                'Nama Pasien',
                'Jenis Kelamin',
                'Alamat',
                'Tanggal Retensi',
                'Tanggal Pemusnahan',
                'Petugas Pemusnah',
                'Status'
            ]);

            // Chunk query
            $query->chunk(100, function ($pemusnahanList) use ($file) {
                foreach ($pemusnahanList as $item) {
                    fputcsv($file, [
                        $item->no_rm,
                        $item->pasien?->nama_pasien ?? '-',
                        $item->pasien?->jenis_kelamin ?? '-',
                        $item->pasien?->alamat ?? '-',
                        $item->tanggal_retensi ? Carbon::parse($item->tanggal_retensi)->format('d/m/Y') : '-',
                        $item->tanggal_pemusnahan ? Carbon::parse($item->tanggal_pemusnahan)->format('d/m/Y H:i:s') : '-',
                        $item->destroyedBy?->nama_lengkap ?? '-',
                        'Dimusnahkan'
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
