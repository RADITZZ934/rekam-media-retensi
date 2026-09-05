<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanPemusnahan;
use App\Models\Pemusnahan;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PengajuanPemusnahanController extends Controller
{
    /**
     * Get list of all SK Submissions
     */
    public function index(Request $request)
    {
        $pengajuans = PengajuanPemusnahan::orderBy('created_at', 'desc')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'no_sk' => $item->no_sk,
                'tanggal_pengajuan' => $item->tanggal_pengajuan ? Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') : '-',
                'ketua_tim' => $item->ketua_tim,
                'anggota_tim' => array_values(array_filter([
                    $item->anggota_tim_1,
                    $item->anggota_tim_2,
                    $item->anggota_tim_3,
                    $item->anggota_tim_4,
                    $item->anggota_tim_5,
                    $item->anggota_tim_6,
                    $item->anggota_tim_7,
                    $item->anggota_tim_8
                ])),
                'jumlah_berkas' => $item->jumlah_berkas,
                'status' => $item->status,
                'keterangan' => $item->keterangan ?? '-',
                'created_at' => $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-'
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $pengajuans
        ]);
    }

    /**
     * Get list of all documents ready to be submitted for destruction (unassigned / not linked to any SK)
     */
    public function getAvailableDocs()
    {
        $docs = Pemusnahan::with(['pasien.kasus'])
            ->where('status', 'dimusnahkan')
            ->whereNull('pengajuan_id')
            ->orderBy('tanggal_retensi', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'no_rm' => $item->no_rm,
                    'nama_pasien' => $item->pasien?->nama_pasien ?? '-',
                    'tanggal_retensi' => $item->tanggal_retensi ? Carbon::parse($item->tanggal_retensi)->format('d/m/Y') : '-',
                    'nama_kasus' => $item->pasien?->kasus?->nama_kasus ?? 'UMUM',
                    'status' => 'Siap Dimusnahkan'
                ];
            });

        return response()->json([
            'success' => true,
            'total' => $docs->count(),
            'data' => $docs
        ]);
    }

    /**
     * Create a new SK Submission (Admin POV)
     */
    public function store(Request $request)
    {
        if (auth()->check() && auth()->user()->role !== 'Administrator') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Administrator yang dapat membuat pengajuan SK Pemusnahan.'
            ], 403);
        }

        $metode = $request->input('metode_pengajuan', 'auto');

        $rules = [
            'no_sk' => 'required|string|max:100|unique:pengajuan_pemusnahan,no_sk',
            'tanggal_pengajuan' => 'required|date',
            'ketua_tim' => 'required|string|max:100',
            'anggota_tim_1' => 'required|string|max:100',
            'anggota_tim_2' => 'nullable|string|max:100',
            'anggota_tim_3' => 'nullable|string|max:100',
            'anggota_tim_4' => 'nullable|string|max:100',
            'anggota_tim_5' => 'nullable|string|max:100',
            'anggota_tim_6' => 'nullable|string|max:100',
            'anggota_tim_7' => 'nullable|string|max:100',
            'anggota_tim_8' => 'nullable|string|max:100',
            'metode_pengajuan' => 'nullable|string|in:auto,manual',
        ];

        if ($metode === 'manual') {
            $rules['file_laporan'] = 'required|file|max:10240';
        }

        $validated = $request->validate($rules);
        unset($validated['metode_pengajuan']);

        $fileValues = [];
        $filename = null;

        if ($request->hasFile('file_laporan')) {
            $file = $request->file('file_laporan');
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($extension, ['csv', 'xlsx'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal: Format file harus berupa CSV atau XLSX.'
                ], 400);
            }

            // Save file
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $destinationPath = public_path('storage/laporan_pemusnahan');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $file->move($destinationPath, $filename);
            $filePath = $destinationPath . '/' . $filename;

            // Parse file values
            if ($extension === 'csv') {
                // Detect delimiter dynamically (comma vs semicolon)
                $delimiter = ';';
                if (($handle = fopen($filePath, "r")) !== FALSE) {
                    $firstLine = fgets($handle);
                    fclose($handle);
                    
                    if ($firstLine !== false) {
                        $numCommas = substr_count($firstLine, ',');
                        $numSemicolons = substr_count($firstLine, ';');
                        if ($numCommas > $numSemicolons) {
                            $delimiter = ',';
                        }
                    }
                }

                if (($handle = fopen($filePath, "r")) !== FALSE) {
                    $headerRow = fgetcsv($handle, 1000, $delimiter);
                    $rmColIndex = 0; // Default to first column
                    
                    if ($headerRow !== FALSE) {
                        // Find the index of No. RM column
                        foreach ($headerRow as $index => $header) {
                            $headerClean = str_replace("\xEF\xBB\xBF", "", $header);
                            $headerClean = strtolower(trim($headerClean));
                            if (in_array($headerClean, ['no_rm', 'no rm', 'nomor rm', 'rekam medis', 'no. rm', 'norm', 'no.rm'])) {
                                $rmColIndex = $index;
                                break;
                            }
                        }
                        
                        // Read subsequent rows
                        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                            if (isset($row[$rmColIndex])) {
                                $fileValues[] = $row[$rmColIndex];
                            }
                        }
                    }
                    fclose($handle);
                }
            } elseif ($extension === 'xlsx') {
                $zip = new \ZipArchive();
                if ($zip->open($filePath) === TRUE) {
                    $sharedStrings = [];
                    $stringsData = $zip->getFromName('xl/sharedStrings.xml');
                    if ($stringsData) {
                        $xml = simplexml_load_string($stringsData);
                        if ($xml) {
                            $xml->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                            $strings = $xml->xpath('//x:si/x:t');
                            if ($strings) {
                                foreach ($strings as $str) {
                                    $sharedStrings[] = (string)$str;
                                }
                            } else {
                                // Fallback if format differs slightly
                                $stringsAlt = $xml->xpath('//x:si');
                                if ($stringsAlt) {
                                    foreach ($stringsAlt as $si) {
                                        $sharedStrings[] = (string)($si->t ?? ($si->r ? $si->r->t : ''));
                                    }
                                }
                            }
                        }
                    }

                    $sheetData = $zip->getFromName('xl/worksheets/sheet1.xml');
                    if ($sheetData) {
                        $xml = simplexml_load_string($sheetData);
                        if ($xml) {
                            $xml->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                            $rows = $xml->xpath('//x:row');
                            if ($rows) {
                                $rmColIndex = 0;
                                $isFirstRow = true;
                                
                                foreach ($rows as $row) {
                                    $cells = $row->xpath('x:c');
                                    if ($cells) {
                                        $rowValues = [];
                                        foreach ($cells as $cell) {
                                            $v = $cell->xpath('x:v');
                                            $val = '';
                                            if ($v) {
                                                $val = (string)$v[0];
                                                $type = (string)$cell['t'];
                                                if ($type === 's') {
                                                    $val = $sharedStrings[$val] ?? '';
                                                }
                                            }
                                            $rowValues[] = $val;
                                        }
                                        
                                        if ($isFirstRow) {
                                            $isFirstRow = false;
                                            foreach ($rowValues as $index => $header) {
                                                $headerClean = str_replace("\xEF\xBB\xBF", "", $header);
                                                $headerClean = strtolower(trim($headerClean));
                                                if (in_array($headerClean, ['no_rm', 'no rm', 'nomor rm', 'rekam medis', 'no. rm', 'norm', 'no.rm'])) {
                                                    $rmColIndex = $index;
                                                    break;
                                                }
                                            }
                                        } else {
                                            if (isset($rowValues[$rmColIndex])) {
                                                $fileValues[] = $rowValues[$rmColIndex];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $zip->close();
                }
            }

            // Clean & normalize values (trim whitespace, remove empty items, remove column headers)
            $cleanedValues = [];
            foreach ($fileValues as $v) {
                $v = trim($v);
                if (empty($v) || in_array(strtolower($v), ['no_rm', 'no rm', 'nomor rm', 'rekam medis', 'no. rm', 'norm', 'no.rm'])) {
                    continue;
                }
                $cleanedValues[] = $v;
                
                // Try to strip non-alphanumeric characters to support dot/dash mismatch formatting
                $stripped = preg_replace('/[^A-Za-z0-9]/', '', $v);
                if ($stripped !== $v && !empty($stripped)) {
                    $cleanedValues[] = $stripped;
                }
            }
            $fileValues = array_unique($cleanedValues);
        }

        // Validate and fetch documents based on selected method
        if ($metode === 'manual') {
            if (empty($fileValues)) {
                if ($filename && file_exists(public_path('storage/laporan_pemusnahan/' . $filename))) {
                    unlink(public_path('storage/laporan_pemusnahan/' . $filename));
                }
                return response()->json([
                    'success' => false,
                    'message' => "Gagal: Kolom Nomor Rekam Medis (No. RM) tidak ditemukan atau kosong di dalam file yang diunggah."
                ], 400);
            }

            // Check against daftar_pemusnahan (status 'dimusnahkan' and not linked to any SK)
            $pendingDocs = Pemusnahan::where('status', 'dimusnahkan')
                ->whereNull('pengajuan_id')
                ->whereIn('no_rm', $fileValues)
                ->get();
                
            if ($pendingDocs->isEmpty()) {
                if ($filename && file_exists(public_path('storage/laporan_pemusnahan/' . $filename))) {
                    unlink(public_path('storage/laporan_pemusnahan/' . $filename));
                }
                
                $parsedSnippet = count($fileValues) > 0 
                    ? implode(', ', array_slice($fileValues, 0, 10)) 
                    : '(Kosong / Tidak terbaca)';
                
                return response()->json([
                    'success' => false,
                    'message' => "Gagal: Tidak ada nomor rekam medis di dalam file yang cocok dengan data pemusnahan yang belum diajukan. Pastikan berkas sudah berstatus Siap Dimusnahkan dan belum masuk SK lain. Data terbaca dari file: [{$parsedSnippet}]"
                ], 400);
            }
        } else {
            // Auto fetch all 'dimusnahkan' documents not yet linked to any SK
            $pendingDocs = Pemusnahan::where('status', 'dimusnahkan')
                ->whereNull('pengajuan_id')
                ->get();

            if ($pendingDocs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal: Tidak ada berkas Rekam Medis siap dimusnahkan yang belum diajukan (antrean kosong).'
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            $pengajuan = PengajuanPemusnahan::create(array_merge($validated, [
                'jumlah_berkas' => $pendingDocs->count(),
                'status' => 'Pending',
                'file_laporan' => $filename
            ]));

            // Link documents to this SK
            Pemusnahan::whereIn('id', $pendingDocs->pluck('id'))
                ->update(['pengajuan_id' => $pengajuan->id]);

            ActivityLogService::log(
                'Pemusnahan',
                'Buat Pengajuan SK',
                "User membuat pengajuan SK Pemusnahan baru dengan No SK: {$pengajuan->no_sk} (Jumlah Berkas: {$pengajuan->jumlah_berkas})"
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan SK Pemusnahan berhasil diajukan.',
                'data' => $pengajuan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($filename && file_exists(public_path('storage/laporan_pemusnahan/' . $filename))) {
                unlink(public_path('storage/laporan_pemusnahan/' . $filename));
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pengajuan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a single SK Submission with list of attached documents
     */
    public function show($id)
    {
        $pengajuan = PengajuanPemusnahan::with(['pemusnahan.pasien'])->find($id);

        if (!$pengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan.'
            ], 404);
        }

        $berkas = $pengajuan->pemusnahan->map(function ($item) {
            return [
                'id' => $item->id,
                'no_rm' => $item->no_rm,
                'nama_pasien' => $item->pasien?->nama_pasien ?? '-',
                'tanggal_retensi' => $item->tanggal_retensi ? Carbon::parse($item->tanggal_retensi)->format('d/m/Y') : '-',
                'status' => $item->status === 'dimusnahkan' ? 'Dimusnahkan' : 'Menunggu Eksekusi'
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pengajuan->id,
                'no_sk' => $pengajuan->no_sk,
                'tanggal_pengajuan' => $pengajuan->tanggal_pengajuan ? Carbon::parse($pengajuan->tanggal_pengajuan)->format('d/m/Y') : '-',
                'ketua_tim' => $pengajuan->ketua_tim,
                'anggota_tim_1' => $pengajuan->anggota_tim_1,
                'anggota_tim_2' => $pengajuan->anggota_tim_2 ?? '-',
                'anggota_tim_3' => $pengajuan->anggota_tim_3 ?? '-',
                'anggota_tim_4' => $pengajuan->anggota_tim_4 ?? '-',
                'anggota_tim_5' => $pengajuan->anggota_tim_5 ?? '-',
                'anggota_tim_6' => $pengajuan->anggota_tim_6 ?? '-',
                'anggota_tim_7' => $pengajuan->anggota_tim_7 ?? '-',
                'anggota_tim_8' => $pengajuan->anggota_tim_8 ?? '-',
                'jumlah_berkas' => $pengajuan->jumlah_berkas,
                'status' => $pengajuan->status,
                'keterangan' => $pengajuan->keterangan ?? '-',
                'file_laporan' => $pengajuan->file_laporan ? asset('storage/laporan_pemusnahan/' . $pengajuan->file_laporan) : null,
                'file_laporan_name' => $pengajuan->file_laporan ? substr($pengajuan->file_laporan, 11) : null,
                'berkas' => $berkas
            ]
        ]);
    }

    /**
     * Approve SK (Director POV)
     */
    public function approve(Request $request, $id)
    {
        if (auth()->check() && !in_array(auth()->user()->role, ['Direktur', 'Administrator'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Direktur atau Administrator yang dapat menyetujui pengajuan SK Pemusnahan.'
            ], 403);
        }

        $pengajuan = PengajuanPemusnahan::findOrFail($id);

        if ($pengajuan->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan dengan status Pending yang dapat disetujui.'
            ], 400);
        }

        $pengajuan->update([
            'status' => 'Approved',
            'keterangan' => $request->keterangan ?? null
        ]);

        ActivityLogService::log(
            'Pemusnahan',
            'Approve Pengajuan SK',
            "Direktur menyetujui pengajuan SK Pemusnahan No SK: {$pengajuan->no_sk}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan SK Pemusnahan berhasil disetujui.'
        ]);
    }

    /**
     * Decline SK (Director POV)
     */
    public function decline(Request $request, $id)
    {
        if (auth()->check() && !in_array(auth()->user()->role, ['Direktur', 'Administrator'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Direktur atau Administrator yang dapat menolak pengajuan SK Pemusnahan.'
            ], 403);
        }

        $pengajuan = PengajuanPemusnahan::findOrFail($id);

        if ($pengajuan->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan dengan status Pending yang dapat ditolak.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $pengajuan->update([
                'status' => 'Declined',
                'keterangan' => $request->keterangan ?? 'Ditolak oleh Direktur.'
            ]);

            // Unlink all connected documents so they can be resubmitted in another SK later
            Pemusnahan::where('pengajuan_id', $pengajuan->id)
                ->update(['pengajuan_id' => null]);

            ActivityLogService::log(
                'Pemusnahan',
                'Decline Pengajuan SK',
                "Direktur menolak pengajuan SK Pemusnahan No SK: {$pengajuan->no_sk} dengan catatan: {$pengajuan->keterangan}"
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan SK Pemusnahan berhasil ditolak, berkas RM dilepaskan dari SK.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pengajuan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate & download Berita Acara Pemusnahan PDF for a specific SK
     */
    public function downloadBA($id)
    {
        $pengajuan = PengajuanPemusnahan::with('pemusnahan.pasien')->findOrFail($id);

        $berkas = $pengajuan->pemusnahan;

        // Get Hari Ini (Name of the day in Indonesian)
        $indonesianDays = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $dayName = Carbon::now()->format('l');
        $hariIni = $indonesianDays[$dayName] ?? $dayName;

        // Formatted dates
        $indonesianMonths = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $now = Carbon::now();
        $tanggalHariIni = $now->day . ' ' . $indonesianMonths[$now->month] . ' ' . $now->year;
        $tanggalSekarang = $tanggalHariIni;

        // Generate PDF using Barryvdh's DomPDF facade
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.berita_acara', [
            'pengajuan' => $pengajuan,
            'berkas' => $berkas,
            'hariIni' => $hariIni,
            'tanggalHariIni' => $tanggalHariIni,
            'tanggalSekarang' => $tanggalSekarang
        ]);

        ActivityLogService::log('Pemusnahan', 'Unduh Berita Acara SK', "User mengunduh Berita Acara Pemusnahan untuk SK No: {$pengajuan->no_sk}");

        return $pdf->download('Berita_Acara_Pemusnahan_SK_' . str_replace('/', '_', $pengajuan->no_sk) . '.pdf');
    }
}
