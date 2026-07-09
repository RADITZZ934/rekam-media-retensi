<?php

namespace App\Http\Controllers;

use App\Models\Retensi;
use App\Models\Pasien;
use App\Models\Kasus;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\ActivityLogService;

class RetensiController extends Controller
{
    /**
     * Hitung status retensi berdasarkan tanggal kunjungan dan kasus
     */
    private function hitungStatusRetensi($tanggalKunjungan, $kasus)
    {
        if (!$kasus || !$tanggalKunjungan) {
            return 'Aktif'; // Default
        }

        $today = Carbon::now();
        $masaAktif = $kasus->masa_retensi_aktif ?? 5;
        $masaInaktif = $kasus->masa_retensi_inaktif ?? 2;

        $batasAktif = Carbon::parse($tanggalKunjungan)->addYears($masaAktif);
        $batasMusnah = $batasAktif->copy()->addYears($masaInaktif);

        if ($today < $batasAktif) {
            return 'Aktif';
        } elseif ($today < $batasMusnah) {
            return 'Inaktif';
        } else {
            return 'Siap Musnah';
        }
    }

    /**
     * Hitung selisih tahun antara tanggal kunjungan dan hari ini
     */
    private function hitungSelisihTahun($tanggalKunjungan)
    {
        if (!$tanggalKunjungan) {
            return 0;
        }
        
        $today = Carbon::now();
        $kunjungan = Carbon::parse($tanggalKunjungan);
        
        return (int) abs($today->diffInYears($kunjungan));
    }

    /**
     * Hitung selisih hari antara tanggal kunjungan dan hari ini
     */
    private function hitungSelisihHari($tanggalKunjungan)
    {
        if (!$tanggalKunjungan) {
            return 0;
        }
        
        $today = Carbon::now()->startOfDay();
        $kunjungan = Carbon::parse($tanggalKunjungan)->startOfDay();
        
        return (int) abs($today->diffInDays($kunjungan));
    }

    /**
     * Hitung countdown hari sebelum berubah ke status berikutnya
     */
    private function hitungCountdown($item)
    {
        if (!$item) {
            return ['days' => null, 'text' => '-'];
        }

        $today = Carbon::now()->startOfDay();
        
        if ($item->status === 'Aktif' && $item->tanggal_batas_aktif) {
            $batas = Carbon::parse($item->tanggal_batas_aktif)->startOfDay();
            if ($today->lessThan($batas)) {
                $diff = (int) abs($today->diffInDays($batas));
                return [
                    'days' => $diff,
                    'text' => "$diff hari lagi sebelum menjadi Inaktif"
                ];
            } else {
                return [
                    'days' => 0,
                    'text' => "Melewati batas aktif (Seharusnya sudah Inaktif)"
                ];
            }
        }

        if ($item->status === 'Inaktif' && $item->tanggal_batas_musnah) {
            $batas = Carbon::parse($item->tanggal_batas_musnah)->startOfDay();
            if ($today->lessThan($batas)) {
                $diff = (int) abs($today->diffInDays($batas));
                return [
                    'days' => $diff,
                    'text' => "$diff hari lagi sebelum Siap Dimusnahkan"
                ];
            } else {
                return [
                    'days' => 0,
                    'text' => "Melewati batas musnah (Seharusnya sudah Siap Dimusnahkan)"
                ];
            }
        }

        if ($item->status === 'Siap Dimusnahkan') {
            return [
                'days' => 0,
                'text' => "Siap untuk dieksekusi pemusnahan"
            ];
        }

        if ($item->status === 'Dimusnahkan') {
            return [
                'days' => null,
                'text' => "Dokumen sudah dimusnahkan"
            ];
        }

        return ['days' => null, 'text' => '-'];
    }

    /**
     * Get list of retensi with filters
     */
    public function index(Request $request)
    {
        $query = Retensi::with(['pasien', 'kasus', 'kunjungan']);

        // Exclude 'Siap Dimusnahkan' by default unless all_statuses is requested or a specific status is filtered
        if ($request->all_statuses !== 'true' && !$request->status) {
            $query->where('status', '!=', 'Siap Dimusnahkan');
        }

        // Search by no_rm or nama_pasien
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('no_rm', 'like', "%{$search}%")
                  ->orWhere('nama_pasien', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by jenis_kasus (kategori)
        if ($request->kategori) {
            $query->whereHas('kasus', function ($q) use ($request) {
                $q->where('kelompok', $request->kategori);
            });
        }

        // Filter by specific kasus_id (jenis_kasus_id)
        if ($request->kasus_id) {
            $query->where('jenis_kasus_id', $request->kasus_id);
        }

        // Filter by tahun kunjungan
        if ($request->tahun) {
            $query->whereYear('tanggal_kunjungan_terakhir', $request->tahun);
        }

        // Pagination
        $perPage = $request->per_page ?? 10;
        $retensiList = $query->orderBy('tanggal_kunjungan_terakhir', 'desc')
                              ->paginate($perPage);

        // Format response
        $data = $retensiList->map(function ($item) {
            $countdown = $this->hitungCountdown($item);
            return [
                'id' => $item->id,
                'no_rm' => $item->pasien?->no_rm ?? $item->no_rm,
                'nama_pasien' => $item->pasien?->nama_pasien ?? '-',
                'nama_kasus' => $item->kasus?->nama_kasus ?? '-',
                'kategori' => $item->kasus?->kelompok ?? '-',
                'jenis_layanan' => $item->jenis_layanan ?? '-',
                'tanggal_kunjungan_terakhir' => $item->tanggal_kunjungan_terakhir ? Carbon::parse($item->tanggal_kunjungan_terakhir)->format('d/m/Y') : '-',
                'masa_aktif' => $item->masa_aktif ?? $item->kasus?->masa_aktif_rj ?? 5,
                'masa_inaktif' => $item->masa_inaktif ?? $item->kasus?->masa_inaktif_rj ?? 2,
                'selisih_tahun' => $this->hitungSelisihTahun($item->tanggal_kunjungan_terakhir),
                'selisih_hari' => $this->hitungSelisihHari($item->tanggal_kunjungan_terakhir),
                'status' => $item->status,
                'tanggal_batas_aktif' => $item->tanggal_batas_aktif ? Carbon::parse($item->tanggal_batas_aktif)->format('d/m/Y') : '-',
                'tanggal_batas_musnah' => $item->tanggal_batas_musnah ? Carbon::parse($item->tanggal_batas_musnah)->format('d/m/Y') : '-',
                'alamat' => $item->pasien?->alamat ?? '-',
                'jenis_kelamin' => $item->pasien?->jenis_kelamin ?? '-',
                'last_update' => $item->updated_at ? Carbon::parse($item->updated_at)->format('d/m/Y H:i') : ($item->tanggal_proses ? Carbon::parse($item->tanggal_proses)->format('d/m/Y H:i') : '-'),
                'countdown_days' => $countdown['days'],
                'countdown_text' => $countdown['text'],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $retensiList->total(),
            'current_page' => $retensiList->currentPage(),
            'last_page' => $retensiList->lastPage(),
            'per_page' => $retensiList->perPage(),
        ]);
    }

    /**
     * Get summary statistics
     */
    public function summary()
    {
        $aktif = Retensi::where('status', 'Aktif')->count();
        $inaktif = Retensi::where('status', 'Inaktif')->count();
        $siapMusnah = Retensi::where('status', 'Siap Dimusnahkan')->count();
        $dimusnahkan = Retensi::where('status', 'Dimusnahkan')->count();

        return response()->json([
            'success' => true,
            'summary' => [
                'aktif' => $aktif,
                'inaktif' => $inaktif,
                'siapMusnah' => $siapMusnah,
                'dimusnahkan' => $dimusnahkan,
                'total' => $aktif + $inaktif + $siapMusnah + $dimusnahkan,
            ],
        ]);
    }

    /**
     * Get detail retensi
     */
    public function show($id)
    {
        $retensi = Retensi::with(['pasien', 'kasus', 'kunjungan'])->find($id);

        if (!$retensi) {
            return response()->json([
                'success' => false,
                'message' => 'Data retensi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $retensi->id,
                'no_rm' => $retensi->pasien?->no_rm ?? $retensi->no_rm,
                'nama_pasien' => $retensi->pasien?->nama_pasien ?? '-',
                'nama_kasus' => $retensi->kasus?->nama_kasus ?? '-',
                'kategori' => $retensi->kasus?->kelompok ?? '-',
                'jenis_layanan' => $retensi->jenis_layanan ?? '-',
                'tanggal_kunjungan_terakhir' => $retensi->tanggal_kunjungan_terakhir ? Carbon::parse($retensi->tanggal_kunjungan_terakhir)->format('d/m/Y') : '-',
                'masa_aktif' => $retensi->masa_aktif ?? $retensi->kasus?->masa_aktif_rj ?? 5,
                'masa_inaktif' => $retensi->masa_inaktif ?? $retensi->kasus?->masa_inaktif_rj ?? 2,
                'selisih_tahun' => $this->hitungSelisihTahun($retensi->tanggal_kunjungan_terakhir),
                'selisih_hari' => $this->hitungSelisihHari($retensi->tanggal_kunjungan_terakhir),
                'status' => $retensi->status,
                'tanggal_batas_aktif' => $retensi->tanggal_batas_aktif ? Carbon::parse($retensi->tanggal_batas_aktif)->format('d/m/Y') : '-',
                'tanggal_batas_musnah' => $retensi->tanggal_batas_musnah ? Carbon::parse($retensi->tanggal_batas_musnah)->format('d/m/Y') : '-',
                'alamat' => $retensi->pasien?->alamat ?? '-',
                'no_telepon' => $retensi->pasien?->no_telepon ?? '-',
                'jenis_kelamin' => $retensi->pasien?->jenis_kelamin ?? '-',
                'last_update' => $retensi->updated_at ? Carbon::parse($retensi->updated_at)->format('d/m/Y H:i') : ($retensi->tanggal_proses ? Carbon::parse($retensi->tanggal_proses)->format('d/m/Y H:i') : '-'),
                'countdown_days' => $this->hitungCountdown($retensi)['days'],
                'countdown_text' => $this->hitungCountdown($retensi)['text'],
            ],
        ]);
    }

    /**
     * Recalculate retensi for all pasien
     */
    public function hitungUlang(Request $request)
    {
        try {
            $updated = 0;
            $retensiService = app(\App\Services\RetensiService::class);
            $pasienList = Pasien::with(['kasus', 'kunjunganTerakhir', 'retensi'])->get();

            foreach ($pasienList as $pasien) {
                if ($retensiService->calculateForPasien($pasien)) {
                    $updated++;
                }
            }

            // Automate import 'Siap Dimusnahkan' records to Pemusnahan queue immediately
            app(\App\Http\Controllers\PemusnahanController::class)->importSiapMusnah();

            ActivityLogService::log('Retensi', 'Hitung Ulang Retensi', "User menghitung ulang status retensi untuk semua pasien");

            return response()->json([
                'success' => true,
                'message' => "Retensi berhasil dihitung ulang untuk {$updated} pasien",
                'updated' => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung ulang retensi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available kategori/jenis kasus
     */
    public function getKategori()
    {
        $kategoriList = Kasus::distinct('kelompok')
                             ->pluck('kelompok')
                             ->filter()
                             ->sort()
                             ->values();

        return response()->json($kategoriList);
    }

    /**
     * Get available tahun dari kunjungan
     */
    public function getTahun()
    {
        $tahunList = Retensi::selectRaw('YEAR(tanggal_kunjungan_terakhir) as tahun')
                             ->distinct()
                             ->whereNotNull('tanggal_kunjungan_terakhir')
                             ->pluck('tahun')
                             ->filter()
                             ->sort()
                             ->values()
                             ->reverse();

        return response()->json($tahunList);
    }

    /**
     * Update data retensi
     */
    public function update(Request $request, $id)
    {
        $retensi = Retensi::find($id);

        if (!$retensi) {
            return response()->json([
                'success' => false,
                'message' => 'Data retensi tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:Aktif,Inaktif,Siap Dimusnahkan,Dimusnahkan',
            'masa_aktif' => 'required|integer|min:0',
            'masa_inaktif' => 'required|integer|min:0',
            'tanggal_kunjungan_terakhir' => 'required|date',
        ]);

        try {
            $tglKunjungan = Carbon::parse($request->tanggal_kunjungan_terakhir);
            $masaAktif = (int) $request->masa_aktif;
            $masaInaktif = (int) $request->masa_inaktif;

            $retensi->update([
                'status' => $request->status,
                'masa_aktif' => $masaAktif,
                'masa_inaktif' => $masaInaktif,
                'tanggal_kunjungan_terakhir' => $tglKunjungan->format('Y-m-d'),
                'tanggal_batas_aktif' => $tglKunjungan->copy()->addYears($masaAktif)->format('Y-m-d'),
                'tanggal_batas_musnah' => $tglKunjungan->copy()->addYears($masaAktif + $masaInaktif)->format('Y-m-d'),
            ]);

            // If status changed to Siap Dimusnahkan, import to Pemusnahan queue immediately
            if ($request->status === 'Siap Dimusnahkan') {
                app(\App\Http\Controllers\PemusnahanController::class)->importSiapMusnah();
            }

            ActivityLogService::log('Retensi', 'Update Retensi', "User mengubah data retensi No RM: {$retensi->no_rm}");

            return response()->json([
                'success' => true,
                'message' => 'Data retensi berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data retensi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export retensi data to CSV format
     */
    public function export(Request $request)
    {
        $query = Retensi::with(['pasien', 'kasus']);

        // Exclude 'Siap Dimusnahkan' by default unless all_statuses is requested or a specific status is filtered
        if ($request->all_statuses !== 'true' && !$request->status) {
            $query->where('status', '!=', 'Siap Dimusnahkan');
        }

        // Apply filters
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('no_rm', 'like', "%{$search}%")
                  ->orWhere('nama_pasien', 'like', "%{$search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->kategori) {
            $query->whereHas('kasus', function ($q) use ($request) {
                $q->where('kelompok', $request->kategori);
            });
        }

        if ($request->kasus_id) {
            $query->where('jenis_kasus_id', $request->kasus_id);
        }

        if ($request->tahun) {
            $query->whereYear('tanggal_kunjungan_terakhir', $request->tahun);
        }

        ActivityLogService::log('Laporan', 'Export CSV Retensi', "User melakukan ekspor CSV Laporan Retensi");

        $filename = 'laporan_retensi_' . Carbon::now()->format('Ymd_His') . '.csv';

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
                'Kategori Kasus',
                'Tanggal Kunjungan Terakhir',
                'Masa Aktif (Tahun)',
                'Masa Inaktif (Tahun)',
                'Tanggal Batas Aktif',
                'Tanggal Batas Musnah',
                'Status Now',
                'Status To'
            ]);

            // Chunk query to save memory
            $query->chunk(100, function ($retensiList) use ($file) {
                foreach ($retensiList as $item) {
                    $statusNow = $item->status;
                    $statusTo = '-';
                    if ($statusNow === 'Aktif') {
                        $statusTo = 'Inaktif';
                    } elseif ($statusNow === 'Inaktif') {
                        $statusTo = 'Siap Dimusnahkan';
                    } elseif ($statusNow === 'Siap Dimusnahkan') {
                        $statusTo = 'Dimusnahkan';
                    }

                    fputcsv($file, [
                        $item->pasien?->no_rm ?? $item->no_rm,
                        $item->pasien?->nama_pasien ?? '-',
                        $item->pasien?->jenis_kelamin ?? '-',
                        $item->pasien?->alamat ?? '-',
                        $item->kasus?->kelompok ?? '-',
                        $item->tanggal_kunjungan_terakhir ? Carbon::parse($item->tanggal_kunjungan_terakhir)->format('d/m/Y') : '-',
                        $item->masa_aktif ?? $item->kasus?->masa_aktif_rj ?? 5,
                        $item->masa_inaktif ?? $item->kasus?->masa_inaktif_rj ?? 2,
                        $item->tanggal_batas_aktif ? Carbon::parse($item->tanggal_batas_aktif)->format('d/m/Y') : '-',
                        $item->tanggal_batas_musnah ? Carbon::parse($item->tanggal_batas_musnah)->format('d/m/Y') : '-',
                        $statusNow,
                        $statusTo
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
