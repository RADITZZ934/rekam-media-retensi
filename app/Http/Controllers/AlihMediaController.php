<?php

namespace App\Http\Controllers;

use App\Models\DokumenRekamMedis;
use App\Models\OCRResult;
use App\Models\Pasien;
use App\Models\Kasus;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AlihMediaController extends Controller
{
    /**
     * Get list of dokumen with filters
     */
    public function index(Request $request)
    {
        $query = DokumenRekamMedis::with(['ocrResult', 'user']);

        // Search by nama_file
        if ($request->search) {
            $query->where('nama_file', 'like', "%{$request->search}%");
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            // Tampilkan semua status agar halaman tidak terlihat kosong saat proses berjalan
            $query->whereIn('status', ['uploaded', 'processing', 'success', 'validated', 'failed']);
        }

        // Filter by engine
        if ($request->engine) {
            $query->where('engine', $request->engine);
        }

        // Pagination
        $perPage = $request->per_page ?? 10;
        $dokumentList = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Format response
        $data = $dokumentList->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_file' => $item->nama_file,
                'tanggal_upload' => $item->created_at ? Carbon::parse($item->created_at)->format('d/m/Y H:i') : '-',
                'status' => $item->status,
                'engine' => $item->engine,
                'no_rm' => $item->no_rm ?? '-',
                'user_name' => $item->user?->nama_lengkap ?? $item->user?->username ?? 'System',
                'error_message' => $item->error_message,
                'ocr_completed' => $item->ocrResult != null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $dokumentList->total(),
            'current_page' => $dokumentList->currentPage(),
            'last_page' => $dokumentList->lastPage(),
            'per_page' => $dokumentList->perPage(),
        ]);
    }
    /**
     * Store manual data entry
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'nama_file' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        try {
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('dokumen_rekam_medis', 'private');
            }

            $dokumen = DokumenRekamMedis::create([
                'nama_file' => $request->nama_file,
                'no_rm' => $request->no_rm,
                'file_original' => $filePath,
                'user_id' => auth()->id() ?? 1,
                'engine' => $request->engine ?? 'manual',
                'status' => 'success',
            ]);

            // If patient data provided, create/update pasien
            if ($request->no_rm && $request->nama_pasien) {
                $jk = $request->jenis_kelamin ?? 'Laki-laki';
                $jk_full = ($jk === 'L' || $jk === 'Laki-laki') ? 'Laki-laki' : 'Perempuan';

                $pasien = Pasien::firstOrCreate(
                    ['no_rm' => $request->no_rm],
                    [
                        'nama_pasien' => $request->nama_pasien,
                        'jenis_kelamin' => $jk_full,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'alamat' => $request->alamat,
                        'status_rm' => 'aktif',
                        'kasus_id' => 1,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Data manual berhasil disimpan',
                'data' => ['id' => $dokumen->id],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function upload(\App\Http\Requests\UploadDokumenRequest $request)
    {
        $request->validated();

        try {
            $dokumenIds = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $originalName = $file->getClientOriginalName();

                    // === STEP 1: Upload — Simpan file original ===
                    $path = $file->store('dokumen_rekam_medis', 'private');
                    $fullPath = storage_path('app/private/' . $path);

                    // === STEP 2: Processing — Convert PDF to Image ===
                    $extension = strtolower($file->getClientOriginalExtension());
                    $compressedPath = $path;

                    if ($extension === 'pdf' && class_exists('Imagick')) {
                        try {
                            $imagick = new \Imagick();
                            $imagick->setResolution(130, 130); // Cukup tinggi untuk hasil OCR akurat, tapi hemat token
                            
                            // Ambil maksimal 5 halaman pertama untuk efisiensi
                            $pdfPath = $fullPath . '[0-4]'; 
                            $imagick->readImage($pdfPath);
                            
                            $pagePaths = [];
                            $baseName = 'converted_' . uniqid();
                            $i = 0;
                            foreach ($imagick as $page) {
                                $page->setImageFormat('jpeg');
                                $page->setImageCompressionQuality(85); // 85% quality untuk teks tajam dan efisiensi token
                                
                                $namabaru = $baseName . "_page_{$i}.jpg";
                                $compressedPagePath = 'dokumen_rekam_medis/' . $namabaru;
                                $page->writeImage(storage_path('app/private/' . $compressedPagePath));
                                $pagePaths[] = $compressedPagePath;
                                $i++;
                            }
                            
                            $imagick->clear();
                            $imagick->destroy();
                            
                            $compressedPath = json_encode($pagePaths);
                        } catch (\Exception $e) {
                            Log::error("Conversion failed: " . $e->getMessage());
                        }
                    }

                    // Save metadata in cache with a temp ID prefix
                    $tempId = 'temp_' . uniqid() . '_' . str_replace('.', '', microtime(true));
                    $tempDoc = [
                        'id' => $tempId,
                        'nama_file' => $originalName,
                        'file_original' => $path,
                        'file_compressed' => $compressedPath,
                        'user_id' => auth()->id() ?? 1,
                        'status' => 'success', // Keep success status so validation page can display it immediately
                        'created_at' => now()->toDateTimeString(),
                    ];

                    Cache::put("temp_doc_{$tempId}", $tempDoc, now()->addHours(2));
                    $dokumenIds[] = $tempId;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diunggah dan dikonversi.',
                'dokumen_ids' => $dokumenIds,
                'redirect_url' => count($dokumenIds) === 1 ? "/validasi-ocr?id={$dokumenIds[0]}" : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload dokumen: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function startOcr($id)
    {
        if (str_starts_with($id, 'temp_')) {
            $cacheData = Cache::get("temp_doc_{$id}");
            if (!$cacheData) {
                return response()->json(['success' => false, 'message' => 'Dokumen tidak ditemukan'], 404);
            }

            try {
                $ocrService = app(\App\Services\OCRService::class);
                $parsedData = $ocrService->processTempDocument($cacheData);

                $cacheData['ocr_result'] = $parsedData;
                $cacheData['status'] = 'success';
                Cache::put("temp_doc_{$id}", $cacheData, now()->addHours(2));

                return response()->json([
                    'success' => true,
                    'message' => 'Proses AI OCR Selesai.',
                    'data' => $parsedData,
                    'raw_json' => json_encode($parsedData, JSON_PRETTY_PRINT)
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses AI: ' . $e->getMessage(),
                ], 500);
            }
        }

        $dokumen = DokumenRekamMedis::find($id);

        if (!$dokumen) {
            return response()->json(['success' => false, 'message' => 'Dokumen tidak ditemukan'], 404);
        }

        try {
            $ocrService = app(\App\Services\OCRService::class);
            $parsedData = $ocrService->processDocument($dokumen);

            return response()->json([
                'success' => true,
                'message' => 'Proses AI OCR Selesai.',
                'data' => $parsedData,
                'raw_json' => json_encode($parsedData, JSON_PRETTY_PRINT)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses AI: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detail dokumen dengan OCR result
     */
    public function show($id)
    {
        if (str_starts_with($id, 'temp_')) {
            $cacheData = Cache::get("temp_doc_{$id}");
            if (!$cacheData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $cacheData['id'],
                    'nama_file' => $cacheData['nama_file'],
                    'tanggal_upload' => Carbon::parse($cacheData['created_at'])->format('d/m/Y H:i'),
                    'status' => $cacheData['status'],
                    'engine' => $cacheData['engine'] ?? 'gemini',
                    'no_rm' => '-',
                    'user_name' => 'System',
                    'error_message' => null,
                    'ocr_result' => isset($cacheData['ocr_result']) ? [
                        'ocr_text' => json_encode($cacheData['ocr_result'], JSON_PRETTY_PRINT),
                        'ai_result' => json_encode($cacheData['ocr_result']),
                        'parsed_data' => json_encode($cacheData['ocr_result']),
                    ] : null,
                ],
            ]);
        }

        $dokumen = DokumenRekamMedis::with('ocrResult')->find($id);

        if (!$dokumen) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $dokumen->id,
                'nama_file' => $dokumen->nama_file,
                'tanggal_upload' => $dokumen->created_at ? Carbon::parse($dokumen->created_at)->format('d/m/Y H:i') : '-',
                'status' => $dokumen->status,
                'engine' => $dokumen->engine,
                'no_rm' => $dokumen->no_rm ?? '-',
                'user_name' => '-', // User diset strip karena relasinya tidak ada
                'error_message' => $dokumen->error_message,
                'ocr_result' => $dokumen->ocrResult ? [
                    'ocr_text' => $dokumen->ocrResult->ocr_text,
                    'ai_result' => $dokumen->ocrResult->ai_result,
                    'parsed_data' => $dokumen->ocrResult->parsed_data,
                ] : null,
            ],
        ]);
    }

    /**
     * Retry OCR untuk dokumen yang failed
     */
    public function retryOCR($id)
    {
        $dokumen = DokumenRekamMedis::find($id);

        if (!$dokumen) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan',
            ], 404);
        }

        try {
            // Update status to processing
            $dokumen->update([
                'status' => 'processing',
                'error_message' => null,
            ]);

            // TODO: Dispatch OCR job
            // dispatch(new ProcessOCRJob($dokumen));

            return response()->json([
                'success' => true,
                'message' => 'OCR sedang diproses ulang',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal retry OCR: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete dokumen
     */
    public function destroy($id)
    {
        if (str_starts_with($id, 'temp_')) {
            $cacheData = Cache::get("temp_doc_{$id}");
            if ($cacheData) {
                // Delete files from storage
                if ($cacheData['file_original']) {
                    Storage::disk('private')->delete($cacheData['file_original']);
                }
                if ($cacheData['file_compressed'] && $cacheData['file_compressed'] !== $cacheData['file_original']) {
                    if (str_starts_with($cacheData['file_compressed'], '[')) {
                        $paths = json_decode($cacheData['file_compressed'], true);
                        if (is_array($paths)) {
                            foreach ($paths as $p) {
                                Storage::disk('private')->delete($p);
                            }
                        }
                    } else {
                        Storage::disk('private')->delete($cacheData['file_compressed']);
                    }
                }
                Cache::forget("temp_doc_{$id}");
            }

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus',
            ]);
        }

        $dokumen = DokumenRekamMedis::find($id);

        if (!$dokumen) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan',
            ], 404);
        }

        try {
            // Delete files from storage
            if ($dokumen->file_original) {
                \Illuminate\Support\Facades\Storage::disk('private')->delete($dokumen->file_original);
            }
            if ($dokumen->file_compressed && $dokumen->file_compressed !== $dokumen->file_original) {
                if (str_starts_with($dokumen->file_compressed, '[')) {
                    $paths = json_decode($dokumen->file_compressed, true);
                    if (is_array($paths)) {
                        foreach ($paths as $p) {
                            \Illuminate\Support\Facades\Storage::disk('private')->delete($p);
                        }
                    }
                } else {
                    \Illuminate\Support\Facades\Storage::disk('private')->delete($dokumen->file_compressed);
                }
            }

            $dokumen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk delete documents
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'ID dokumen tidak valid',
            ], 400);
        }

        try {
            $dokumens = DokumenRekamMedis::whereIn('id', $ids)->get();

            foreach ($dokumens as $dokumen) {
                if ($dokumen->file_original) {
                    \Illuminate\Support\Facades\Storage::disk('private')->delete($dokumen->file_original);
                }
                if ($dokumen->file_compressed && $dokumen->file_compressed !== $dokumen->file_original) {
                    if (str_starts_with($dokumen->file_compressed, '[')) {
                        $paths = json_decode($dokumen->file_compressed, true);
                        if (is_array($paths)) {
                            foreach ($paths as $p) {
                                \Illuminate\Support\Facades\Storage::disk('private')->delete($p);
                            }
                        }
                    } else {
                        \Illuminate\Support\Facades\Storage::disk('private')->delete($dokumen->file_compressed);
                    }
                }
                $dokumen->delete();
            }

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' dokumen berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus beberapa dokumen: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get status counts
     */
    public function summary()
    {
        $uploaded = DokumenRekamMedis::where('status', 'uploaded')->count();
        $processing = DokumenRekamMedis::where('status', 'processing')->count();
        $success = DokumenRekamMedis::where('status', 'success')->count();
        $failed = DokumenRekamMedis::where('status', 'failed')->count();

        return response()->json([
            'success' => true,
            'summary' => [
                'uploaded' => $uploaded,
                'processing' => $processing,
                'success' => $success,
                'failed' => $failed,
                'total' => $uploaded + $processing + $success + $failed,
            ],
        ]);
    }

    /**
     * Get list of completed dokumen for validasi OCR
     */
    public function getCompleted()
    {
        $dokumen = DokumenRekamMedis::where('status', 'success')
            ->with('ocrResult')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $dokumen->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_file' => $item->nama_file,
                'tanggal_upload' => $item->created_at ? Carbon::parse($item->created_at)->format('d/m/Y H:i') : '-',
                'status' => $item->status,
                'engine' => $item->engine,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get OCR text for a dokumen
     */
    public function getOcrText($id)
    {
        if (str_starts_with($id, 'temp_')) {
            $cacheData = Cache::get("temp_doc_{$id}");
            if (!$cacheData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan',
                ], 404);
            }

            $metadata = $cacheData['ocr_result'] ?? [];
            $ocrText = json_encode($metadata, JSON_PRETTY_PRINT);

            return response()->json([
                'success' => true,
                'text' => $ocrText,
                'engine' => 'gemini',
                'metadata' => [
                    'nomor_rm' => $metadata['nomor_rm'] ?? '',
                    'nama_pasien' => $metadata['nama_pasien'] ?? '',
                    'jenis_kelamin' => $metadata['jenis_kelamin'] ?? '',
                    'tanggal_lahir' => $metadata['tanggal_lahir'] ?? '',
                    'tempat_lahir' => $metadata['tempat_lahir'] ?? '',
                    'alamat' => $metadata['alamat'] ?? '',
                    'tanggal_masuk' => $metadata['tanggal_masuk'] ?? '',
                    'tanggal_keluar' => $metadata['tanggal_keluar'] ?? '',
                    'diagnosis' => $metadata['diagnosis'] ?? '',
                    'nama_kasus' => $metadata['nama_kasus'] ?? '',
                    'keterangan' => $metadata['keterangan'] ?? '',
                ],
            ]);
        }

        $dokumen = DokumenRekamMedis::with('ocrResult')->find($id);

        if (!$dokumen) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan',
            ], 404);
        }

        $ocrText = $dokumen->ocrResult?->ocr_text ?? '';
        $metadata = $dokumen->ocrResult?->parsed_data ?? [];

        // Decode parsed_data jika masih JSON string
        if (is_string($metadata)) {
            $metadata = json_decode($metadata, true) ?? [];
        }

        return response()->json([
            'success' => true,
            'text' => $ocrText,
            'engine' => $dokumen->engine ?? $dokumen->ocrResult?->engine ?? null,
            'metadata' => [
                'nomor_rm' => $metadata['nomor_rm'] ?? '',
                'nama_pasien' => $metadata['nama_pasien'] ?? '',
                'jenis_kelamin' => $metadata['jenis_kelamin'] ?? '',
                'tanggal_lahir' => $metadata['tanggal_lahir'] ?? '',
                'tempat_lahir' => $metadata['tempat_lahir'] ?? '',
                'alamat' => $metadata['alamat'] ?? '',
                'tanggal_masuk' => $metadata['tanggal_masuk'] ?? '',
                'tanggal_keluar' => $metadata['tanggal_keluar'] ?? '',
                'diagnosis' => $metadata['diagnosis'] ?? '',
                'nama_kasus' => $metadata['nama_kasus'] ?? '',
                'keterangan' => $metadata['keterangan'] ?? '',
            ],
        ]);
    }

    /**
     * Save draft validasi OCR
     */
    public function saveDraft(Request $request, $id)
    {
        if (str_starts_with($id, 'temp_')) {
            $cacheData = Cache::get("temp_doc_{$id}");
            if (!$cacheData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan',
                ], 404);
            }

            try {
                $cacheData['ocr_result'] = $request->metadata ?? [];
                Cache::put("temp_doc_{$id}", $cacheData, now()->addHours(2));

                return response()->json([
                    'success' => true,
                    'message' => 'Draft berhasil disimpan',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan draft: ' . $e->getMessage(),
                ], 500);
            }
        }

        $dokumen = DokumenRekamMedis::find($id);

        if (!$dokumen) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan',
            ], 404);
        }

        try {
            $ocrResult = OCRResult::firstOrCreate(
                ['dokumen_id' => $id],
                [
                    'ocr_text' => $request->ocrText ?? '',
                    'parsed_data' => $request->metadata ?? [],
                    'status' => 'draft',
                ]
            );

            // Update if exists
            if ($ocrResult->wasRecentlyCreated === false) {
                $ocrResult->update([
                    'ocr_text' => $request->ocrText ?? '',
                    'parsed_data' => $request->metadata ?? [],
                    'status' => 'draft',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Draft berhasil disimpan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan draft: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit validasi OCR
     */
    public function submitValidasi(Request $request, $id)
    {
        if (str_starts_with($id, 'temp_')) {
            $cacheData = Cache::get("temp_doc_{$id}");
            if (!$cacheData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan atau sudah kedaluwarsa',
                ], 404);
            }

            try {
                $metadata = $request->metadata;
                $no_rm = $metadata['nomor_rm'] ?? null;

                if (!$no_rm) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nomor RM harus diisi',
                    ], 400);
                }

                // 1. Find or Create Pasien
                $pasien = Pasien::find($no_rm);
                if (!$pasien) {
                    // Find matching Case if possible
                    $kasus = null;
                    if (!empty($metadata['nama_kasus'])) {
                        $kasus = Kasus::where('jenis_kasus', 'like', "%{$metadata['nama_kasus']}%")->first();
                    }

                    $jk = $metadata['jenis_kelamin'] ?? 'L';
                    $jk_full = ($jk === 'L' || $jk === 'Laki-laki') ? 'Laki-laki' : 'Perempuan';

                    $pasien = Pasien::create([
                        'no_rm' => $no_rm,
                        'nama_pasien' => $metadata['nama_pasien'] ?? 'PASIEN OCR',
                        'jenis_kelamin' => $jk_full,
                        'tanggal_lahir' => $metadata['tanggal_lahir'] ?? null,
                        'tempat_lahir' => $metadata['tempat_lahir'] ?? null,
                        'alamat' => $metadata['alamat'] ?? null,
                        'status_rm' => 'Aktif',
                        'kasus_id' => $metadata['kasus_id'] ?? $kasus->id ?? 1, // Default to selected case, then matched case, then first case
                    ]);
                } else {
                    if (!empty($metadata['kasus_id'])) {
                        $pasien->update(['kasus_id' => $metadata['kasus_id']]);
                    }
                }

                // 2. Create Kunjungan
                $kunjungan = Kunjungan::create([
                    'no_rm' => $no_rm,
                    'tanggal_masuk' => $metadata['tanggal_masuk'] ?? Carbon::now(),
                    'tanggal_keluar' => $metadata['tanggal_keluar'] ?? null,
                    'diagnosa' => ($metadata['diagnosis'] ?? $metadata['nama_kasus'] ?? 'Diagnosa Alih Media') . " (" . ($metadata['keterangan'] ?? '') . ")",
                ]);

                // 3. Create DokumenRekamMedis first
                $dokumen = DokumenRekamMedis::create([
                    'nama_file' => $cacheData['nama_file'],
                    'file_original' => $cacheData['file_original'],
                    'file_compressed' => $cacheData['file_compressed'],
                    'user_id' => $cacheData['user_id'] ?? (auth()->id() ?? 1),
                    'status' => 'validated',
                    'engine' => 'gemini',
                    'no_rm' => $no_rm,
                ]);

                // 4. Create OCR Result
                OCRResult::create([
                    'dokumen_id' => $dokumen->id,
                    'ocr_text' => $request->ocrText ?? json_encode($metadata, JSON_PRETTY_PRINT),
                    'parsed_data' => $metadata,
                    'ai_result' => $metadata,
                    'status' => 'validated',
                    'engine' => 'gemini',
                    'validated_at' => Carbon::now(),
                ]);

                // 5. Save ValidasiData log
                \App\Models\ValidasiData::create([
                    'dokumen_id' => $dokumen->id,
                    'no_rm' => $no_rm,
                    'nama_pasien' => $metadata['nama_pasien'] ?? null,
                    'tanggal_lahir' => $metadata['tanggal_lahir'] ?? null,
                    'tempat_lahir' => $metadata['tempat_lahir'] ?? null,
                    'jenis_kelamin' => $metadata['jenis_kelamin'] ?? null,
                    'alamat' => $metadata['alamat'] ?? $metadata['alamat_pasien'] ?? null,
                    'no_telepon' => $metadata['no_telepon'] ?? null,
                    'tanggal_masuk' => $metadata['tanggal_masuk'] ?? null,
                    'tanggal_keluar' => $metadata['tanggal_keluar'] ?? null,
                    'diagnosa' => $metadata['diagnosis'] ?? $metadata['diagnosa'] ?? null,
                    'dokter' => $metadata['dokter'] ?? $metadata['dokter_dpjp'] ?? null,
                    'kasus_id' => $metadata['kasus_id'] ?? null,
                    'verified_by' => auth()->id() ?? 1,
                ]);

                // 6. Trigger Automatic Retention Calculation
                $retensiService = app(\App\Services\RetensiService::class);
                $retensiService->calculateForPasien($pasien);

                // Delete cache entry
                Cache::forget("temp_doc_{$id}");

                return response()->json([
                    'success' => true,
                    'message' => 'Validasi berhasil disimpan, data pasien dan kunjungan telah diperbarui.',
                ]);
            } catch (\Exception $e) {
                Log::error('Submit Validasi Error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal submit validasi: ' . $e->getMessage(),
                ], 500);
            }
        }

        $dokumen = DokumenRekamMedis::find($id);

        if (!$dokumen) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan',
            ], 404);
        }

        try {
            $metadata = $request->metadata;
            $no_rm = $metadata['nomor_rm'] ?? null;

            if (!$no_rm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor RM harus diisi',
                ], 400);
            }

            // 1. Find or Create Pasien
            $pasien = Pasien::find($no_rm);
            if (!$pasien) {
                // Find matching Case if possible
                $kasus = null;
                if (!empty($metadata['nama_kasus'])) {
                    $kasus = Kasus::where('jenis_kasus', 'like', "%{$metadata['nama_kasus']}%")->first();
                }

                $jk = $metadata['jenis_kelamin'] ?? 'L';
                $jk_full = ($jk === 'L' || $jk === 'Laki-laki') ? 'Laki-laki' : 'Perempuan';

                $pasien = Pasien::create([
                    'no_rm' => $no_rm,
                    'nama_pasien' => $metadata['nama_pasien'] ?? 'PASIEN OCR',
                    'jenis_kelamin' => $jk_full,
                    'tanggal_lahir' => $metadata['tanggal_lahir'] ?? null,
                    'tempat_lahir' => $metadata['tempat_lahir'] ?? null,
                    'alamat' => $metadata['alamat'] ?? null,
                    'status_rm' => 'Aktif',
                    'kasus_id' => $metadata['kasus_id'] ?? $kasus->id ?? 1,
                ]);
            } else {
                if (!empty($metadata['kasus_id'])) {
                    $pasien->update(['kasus_id' => $metadata['kasus_id']]);
                }
            }

            // 2. Create Kunjungan
            $kunjungan = Kunjungan::create([
                'no_rm' => $no_rm,
                'tanggal_masuk' => $metadata['tanggal_masuk'] ?? Carbon::now(),
                'tanggal_keluar' => $metadata['tanggal_keluar'] ?? null,
                'diagnosa' => ($metadata['diagnosis'] ?? $metadata['nama_kasus'] ?? 'Diagnosa Alih Media') . " (" . ($metadata['keterangan'] ?? '') . ")",
            ]);

            // 3. Update OCR Result
            $ocrResult = OCRResult::updateOrCreate(
                ['dokumen_id' => $id],
                [
                    'ocr_text' => $request->ocrText ?? '',
                    'parsed_data' => $metadata,
                    'status' => 'validated',
                    'validated_at' => Carbon::now(),
                ]
            );

            // 4. Update Dokumen status & link to RM
            $dokumen->update([
                'status' => 'validated',
                'no_rm' => $no_rm,
            ]);

            // 5. Save ValidasiData log
            \App\Models\ValidasiData::create([
                'dokumen_id' => $id,
                'no_rm' => $no_rm,
                'nama_pasien' => $metadata['nama_pasien'] ?? null,
                'tanggal_lahir' => $metadata['tanggal_lahir'] ?? null,
                'tempat_lahir' => $metadata['tempat_lahir'] ?? null,
                'jenis_kelamin' => $metadata['jenis_kelamin'] ?? null,
                'alamat' => $metadata['alamat'] ?? $metadata['alamat_pasien'] ?? null,
                'no_telepon' => $metadata['no_telepon'] ?? null,
                'tanggal_masuk' => $metadata['tanggal_masuk'] ?? null,
                'tanggal_keluar' => $metadata['tanggal_keluar'] ?? null,
                'diagnosa' => $metadata['diagnosis'] ?? $metadata['diagnosa'] ?? null,
                'dokter' => $metadata['dokter'] ?? $metadata['dokter_dpjp'] ?? null,
                'kasus_id' => $metadata['kasus_id'] ?? null,
                'verified_by' => auth()->id() ?? 1,
            ]);

            // 6. Trigger Automatic Retention Calculation
            $retensiService = app(\App\Services\RetensiService::class);
            $retensiService->calculateForPasien($pasien);

            return response()->json([
                'success' => true,
                'message' => 'Validasi berhasil disimpan, data pasien dan kunjungan telah diperbarui.',
            ]);
        } catch (\Exception $e) {
            Log::error('Submit Validasi Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal submit validasi: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Parse free text (from Gemini Web chat) into structured JSON.
     */
    public function parseAiText(Request $request)
    {
        $text = $request->input('text');

        if (!$text) {
            return response()->json([
                'success' => false,
                'message' => 'Teks tidak boleh kosong'
            ], 400);
        }

        try {
            $geminiClient = app(\App\Services\GeminiDirectClient::class);

            $prompt = "Tolong ekstrak data rekam medis dari teks berikut menjadi format JSON murni.\n\n"
                . "--- TEKS START ---\n"
                . $text . "\n"
                . "--- TEKS END ---\n\n"
                . "Field JSON: nomor_rm, nama_pasien, tanggal_lahir, jenis_kelamin, alamat, "
                . "tanggal_masuk, tanggal_keluar, diagnosis, nama_kasus, keterangan. "
                . "Kembalikan HANYA JSON murni tanpa markdown.";

            $content = $geminiClient->chat($prompt);

            // Cleanup response
            $content = preg_replace('/```json\s*/', '', $content);
            $content = preg_replace('/```\s*/', '', $content);
            $content = trim($content);

            $parsed = json_decode($content, true);

            if (!$parsed) {
                throw new \Exception("Gagal mendecode JSON dari AI: " . substr($content, 0, 100));
            }

            return response()->json([
                'success' => true,
                'data' => $parsed
            ]);

        } catch (\Exception $e) {
            Log::error('Parse AI Text Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses teks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Interactive Chat with AI (General or Document-specific).
     */
    public function chatWithAi(Request $request)
    {
        $message = $request->input('message');
        $context = $request->input('context'); // Opsional: teks dokumen saat ini

        if (!$message)
            return response()->json(['success' => false, 'message' => 'Pesan kosong'], 400);

        try {
            $geminiClient = app(\App\Services\GeminiDirectClient::class);

            $systemInstruction = "Kamu adalah RSUK AI Assistant. Bantu petugas rekam medis mengelola data. "
                . "Jujur, sopan, dan ringkas. Jika ada konteks teks dokumen, gunakan untuk menjawab.";

            $prompt = "";
            if ($context) {
                $prompt .= "--- CONTEXT DOCUMENT ---\n" . $context . "\n--- END CONTEXT ---\n\n";
            }
            $prompt .= $systemInstruction . "\n\nPertanyaan User: " . $message;

            $response = $geminiClient->chat($prompt, $systemInstruction);

            return response()->json([
                'success' => true,
                'response' => $response
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Serve file for preview
     */
    public function getFile(Request $request, $id)
    {
        if (str_starts_with($id, 'temp_')) {
            $cacheData = Cache::get("temp_doc_{$id}");
            if (!$cacheData) {
                abort(404);
            }

            if ($request->has('original') || (str_ends_with(strtolower($cacheData['nama_file']), '.pdf') && !$request->has('image'))) {
                $path = $cacheData['file_original'];
            } else {
                $path = $cacheData['file_compressed'] ?? $cacheData['file_original'];
                if ($path && str_starts_with($path, '[')) {
                    $paths = json_decode($path, true);
                    $path = $paths[0] ?? $cacheData['file_original'];
                }
            }
        } else {
            $dokumen = DokumenRekamMedis::find($id);

            if (!$dokumen) {
                abort(404);
            }

            // If 'original' is requested or it's a PDF and we want native viewer
            if ($request->has('original') || (str_ends_with(strtolower($dokumen->nama_file), '.pdf') && !$request->has('image'))) {
                $path = $dokumen->file_original;
            } else {
                // Default: prefer compressed image for fast loading/OCR preview
                $path = $dokumen->file_compressed ?? $dokumen->file_original;
                if ($path && str_starts_with($path, '[')) {
                    $paths = json_decode($path, true);
                    $path = $paths[0] ?? $dokumen->file_original;
                }
            }
        }

        if (!$path || !\Illuminate\Support\Facades\Storage::disk('private')->exists($path)) {
            if (isset($dokumen)) {
                // Final fallback
                $path = $dokumen->file_original;
            } elseif (isset($cacheData)) {
                $path = $cacheData['file_original'];
            }
            if (!$path || !\Illuminate\Support\Facades\Storage::disk('private')->exists($path)) {
                abort(404);
            }
        }

        return \Illuminate\Support\Facades\Storage::disk('private')->response($path);
    }
}
