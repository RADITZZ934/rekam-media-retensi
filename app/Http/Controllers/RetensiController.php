<?php

namespace App\Http\Controllers;

use App\Models\Retensi;
use App\Models\Pasien;
use App\Models\Kasus;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        
        return $today->diffInYears($kunjungan);
    }

    /**
     * Get list of retensi with filters
     */
    public function index(Request $request)
    {
        $query = Retensi::with(['pasien', 'kasus', 'kunjungan']);

        // Search by no_rm or nama_pasien
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('no_rm', 'like', "%{$search}%")
                  ->orWhere('nama_pasien', 'like', "%{$search}%");
            });
        }

        // Filter by status retensi
        if ($request->status) {
            $query->where('status_retensi', $request->status);
        }

        // Filter by jenis_kasus (kategori)
        if ($request->kategori) {
            $query->whereHas('kasus', function ($q) use ($request) {
                $q->where('kategori', $request->kategori);
            });
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
            return [
                'id' => $item->id,
                'no_rm' => $item->pasien?->no_rm ?? $item->no_rm,
                'nama_pasien' => $item->pasien?->nama_pasien ?? '-',
                'nama_kasus' => $item->kasus?->nama_kasus ?? '-',
                'kategori' => $item->kasus?->kelompok ?? '-',
                'jenis_layanan' => $item->jenis_layanan ?? '-',
                'tanggal_kunjungan_terakhir' => $item->tanggal_kunjungan_terakhir ? Carbon::parse($item->tanggal_kunjungan_terakhir)->format('d/m/Y') : '-',
                'masa_aktif' => $item->kasus?->masa_aktif_rj ?? 5,
                'masa_inaktif' => $item->kasus?->masa_inaktif_rj ?? 2,
                'selisih_tahun' => $this->hitungSelisihTahun($item->tanggal_kunjungan_terakhir),
                'status_retensi' => $item->status_retensi,
                'tanggal_batas_aktif' => $item->tanggal_batas_aktif ? Carbon::parse($item->tanggal_batas_aktif)->format('d/m/Y') : '-',
                'tanggal_batas_musnah' => $item->tanggal_batas_musnah ? Carbon::parse($item->tanggal_batas_musnah)->format('d/m/Y') : '-',
                'alamat' => $item->pasien?->alamat ?? '-',
                'jenis_kelamin' => $item->pasien?->jenis_kelamin ?? '-',
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
        $aktif = Retensi::where('status_retensi', 'Aktif')->count();
        $inaktif = Retensi::where('status_retensi', 'Inaktif')->count();
        $siapMusnah = Retensi::where('status_retensi', 'Siap Musnah')->count();

        return response()->json([
            'success' => true,
            'summary' => [
                'aktif' => $aktif,
                'inaktif' => $inaktif,
                'siapMusnah' => $siapMusnah,
                'total' => $aktif + $inaktif + $siapMusnah,
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
                'masa_aktif' => $retensi->kasus?->masa_aktif_rj ?? 5,
                'masa_inaktif' => $retensi->kasus?->masa_inaktif_rj ?? 2,
                'selisih_tahun' => $this->hitungSelisihTahun($retensi->tanggal_kunjungan_terakhir),
                'status_retensi' => $retensi->status_retensi,
                'tanggal_batas_aktif' => $retensi->tanggal_batas_aktif ? Carbon::parse($retensi->tanggal_batas_aktif)->format('d/m/Y') : '-',
                'tanggal_batas_musnah' => $retensi->tanggal_batas_musnah ? Carbon::parse($retensi->tanggal_batas_musnah)->format('d/m/Y') : '-',
                'alamat' => $retensi->pasien?->alamat ?? '-',
                'no_telepon' => $retensi->pasien?->no_telepon ?? '-',
                'jenis_kelamin' => $retensi->pasien?->jenis_kelamin ?? '-',
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
            $pasienList = Pasien::all();

            foreach ($pasienList as $pasien) {
                if ($retensiService->calculateForPasien($pasien)) {
                    $updated++;
                }
            }

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
}
