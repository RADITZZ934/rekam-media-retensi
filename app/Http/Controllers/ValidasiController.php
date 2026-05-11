<?php

namespace App\Http\Controllers;

use App\Models\DokumenRekamMedis;
use App\Models\OCRResult;
use App\Models\Pasien;
use App\Models\Kunjungan;
use App\Models\ValidasiData;
use App\Services\RetentionService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ValidasiRequest;

class ValidasiController extends Controller
{
    /**
     * Display OCR Results ready for Validation
     */
    public function show($id)
    {
        $dokumen = DokumenRekamMedis::with('ocrResult')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $dokumen
        ]);
    }

    /**
     * Process user validasi
     */
    public function store(ValidasiRequest $request, RetentionService $retentionService)
    {
        try {
            DB::beginTransaction();
            
            $dokumen = DokumenRekamMedis::findOrFail($request->dokumen_id);
            $valData = $request->validated();
            
            // Simpan Data Pasien 
            $pasien = Pasien::updateOrCreate(
                ['no_rm' => $valData['no_rm']],
                [
                    'nama_pasien' => $valData['nama_pasien'],
                    'jenis_kelamin' => $valData['jenis_kelamin'],
                    'tanggal_lahir' => $valData['tanggal_lahir'],
                    'tempat_lahir' => $valData['tempat_lahir'] ?? null,
                    'alamat' => $valData['alamat'] ?? null,
                    'no_telepon' => $valData['no_telepon'] ?? null,
                    'status_rm' => 'Aktif',
                ]
            );

            // Simpan Data Kunjungan
            $kunjungan = Kunjungan::create([
                'no_rm' => $pasien->no_rm,
                'nama_pasien' => $pasien->nama_pasien,
                'jenis_kelamin' => $pasien->jenis_kelamin,
                'alamat' => $pasien->alamat,
                'tanggal_masuk' => $valData['tanggal_masuk'],
                'tanggal_keluar' => $valData['tanggal_keluar'] ?? null,
                'diagnosa' => $valData['diagnosa'],
            ]);

            // Catat log di validasi
            ValidasiData::create(array_merge($valData, ['verified_by' => auth()->id()]));

            // Update FK DokumenRekamMedis
            $dokumen->update(['no_rm' => $pasien->no_rm]);

            // Otomatis Hitung Retensi menggunakan Service
            $retentionService->calculateForPasien($pasien);
            
            ActivityLogService::log('Validasi', 'Approve Data OCR', "User memvalidasi dokumen ID: {$dokumen->id}");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data validasi berhasil disimpan, Relasi Retensi telah direkalculasi.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            ActivityLogService::log('Validasi', 'Error', "Error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal disimpan: ' . $e->getMessage()
            ], 500);
        }
    }
}
