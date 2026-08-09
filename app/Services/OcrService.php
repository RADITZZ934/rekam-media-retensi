<?php

namespace App\Services;

use App\Models\DokumenRekamMedis;
use App\Models\OCRResult;
use Illuminate\Support\Facades\Log;
use Throwable;

class OCRService
{
    protected GeminiDirectClient $geminiClient;

    public function __construct(GeminiDirectClient $geminiClient)
    {
        $this->geminiClient = $geminiClient;
    }

    /**
     * Proses OCR Utama dengan konsep "Background AI Chat" (via Google Gemini Vision)
     */
    public function processDocument(DokumenRekamMedis $dokumen)
    {
        try {
            $dokumen->update(['status' => 'processing', 'error_message' => null]);

            // Eksekusi Gemini Vision
            $result = $this->callGeminiVision($dokumen);

            // Simpan hasil OCR ke ocr_result
            $dokumen->ocrResult()->updateOrCreate(
                ['dokumen_id' => $dokumen->id],
                [
                    'ocr_text' => json_encode($result['parsed_data'], JSON_PRETTY_PRINT),
                    'ai_result' => json_encode($result['parsed_data']),
                    'parsed_data' => json_encode($result['parsed_data']),
                    'engine' => 'gemini',
                    'status' => 'success',
                ]
            );

            // Update dokumen status = success
            $dokumen->update([
                'status' => 'success',
                'engine' => 'gemini',
                'error_message' => null,
            ]);

            return $result['parsed_data'];

        } catch (Throwable $e) {
            Log::error("OCR Background Process Failed (Gemini): " . $e->getMessage());
            $dokumen->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Proses OCR untuk dokumen sementara (tidak menulis ke database)
     */
    public function processTempDocument(array $meta)
    {
        $dokumen = new DokumenRekamMedis();
        $dokumen->nama_file = $meta['nama_file'] ?? null;
        $dokumen->file_original = $meta['file_original'];
        $dokumen->file_compressed = $meta['file_compressed'];
        
        $result = $this->callGeminiVision($dokumen);
        return $result['parsed_data'];
    }

    /**
     * Komunikasi dengan Google Gemini Vision
     */
    private function callGeminiVision(DokumenRekamMedis $dokumen): array
    {
        // Mock Interceptor Logic
        if (\App\Models\AppSetting::get('mock_ai_interceptor', 'false') === 'true') {
            $originalFileName = $dokumen->nama_file;
            $normalizedName = strtoupper(str_replace('_', ' ', $originalFileName));
            
            $mockMapping = [
                'ASRI' => 'RM_ASRI.json',
                'OLIVIA' => 'RM_BY_OLIVIA_CHRISANTI_TARDIANTO.json',
                'ERNA' => 'RM_ERNA_TRI.json',
                'NURLIZA' => 'RM_M_NURLIZA.json',
                'RINA' => 'RM_RINA_LESTARI.json',
                'SUNARSO' => 'RM_SUNARSO.json',
                'SUYATI' => 'RM_SUYATI.json',
                'SUYITNO' => 'RM_SUYITNO.json',
            ];

            $mockFileName = null;
            if ($originalFileName) {
                foreach ($mockMapping as $mockKey => $fileName) {
                    if (str_contains($normalizedName, $mockKey)) {
                        $mockFileName = $fileName;
                        break;
                    }
                }
            }

            if ($mockFileName) {
                $mockPath = base_path('document-ai-service/mock/' . $mockFileName);
                
                if (file_exists($mockPath)) {
                    // Simulate AI processing delay (3 to 5 seconds)
                    sleep(rand(3, 5));
                    
                    $mockContent = file_get_contents($mockPath);
                    $parsedData = json_decode($mockContent, true);
                    
                    if ($parsedData) {
                        Log::info("Mock AI Interceptor matched: {$originalFileName} -> {$mockFileName}");
                        return [
                            'parsed_data' => $parsedData,
                        ];
                    }
                }
            }
        }

        $compressedField = $dokumen->file_compressed;
        $images = [];
        $mimeType = 'image/jpeg'; // Default for converted pages

        if ($compressedField && str_starts_with($compressedField, '[')) {
            $decoded = json_decode($compressedField, true);
            if (is_array($decoded)) {
                foreach ($decoded as $relPath) {
                    $fullPath = storage_path('app/private/' . $relPath);
                    if (file_exists($fullPath)) {
                        $images[] = [
                            'mimeType' => mime_content_type($fullPath),
                            'data' => base64_encode(file_get_contents($fullPath))
                        ];
                    }
                }
            }
        }

        // Fallback to single compressed file or original file
        if (empty($images)) {
            $path = storage_path('app/private/' . ($dokumen->file_compressed ?? $dokumen->file_original));
            if (!file_exists($path)) {
                throw new \Exception("File dokumen tidak ditemukan.");
            }
            $mimeType = mime_content_type($path);
            $images = base64_encode(file_get_contents($path));
        }

        $systemInstruction = "Tugasmu adalah mengekstrak data rekam medis dari gambar menjadi JSON murni secara sangat teliti.\n\n"
            . "INSTRUKSI KHUSUS:\n"
            . "1. Dokumen ini terdiri dari beberapa halaman rekam medis. Halaman awal biasanya berisi data identitas Ibu atau data registrasi umum, sedangkan halaman berikutnya berisi data bayi baru lahir (BBL) atau sebaliknya.\n"
            . "2. Carilah data PASIEN UTAMA (jika nama pasien diawali dengan 'By' atau 'By. Ny.', maka pasien utama adalah BAYI tersebut). Gunakan format nama pasien yang lengkap dan benar, misalnya 'By Ny. Olivia Chrisanti Tardianto'. Perhatikan ejaan nama pasien secara teliti dan hindari kesalahan seperti membaca 'Olivia' menjadi 'Obuta' atau 'Dhira'.\n"
            . "3. Pastikan mengekstrak nomor rekam medis (NOMOR RM) dengan sangat teliti (biasanya berupa angka 6 digit seperti '209773'). Cross-check antara seluruh halaman untuk memastikan nomor RM yang benar dan hindari salah baca angka (contoh: jangan membaca '209773' menjadi '209972' atau '203793').\n"
            . "4. Untuk field 'jenis_kelamin' di 'identitas_pasien', carilah JENIS KELAMIN DARI BAYI (pasien utama). Jika di lembar BBL/bayi tertulis jenis kelamin laki-laki atau 'L', maka isi dengan 'L' (Laki-laki). HATI-HATI: Jangan mencampuradukkan jenis kelamin Ibu (Perempuan) dengan jenis kelamin Bayi (Laki-laki)!\n"
            . "5. Cari ALAMAT di seluruh bagian dokumen (gabungkan informasi dari semua halaman). Untuk bayi, alamat biasanya mengikuti alamat Ibu atau Wali. Ambil dari sana jika alamat pasien tidak tertulis eksplisit.\n"
            . "6. Ekstrak nama Ibu di bagian 'identitas_ibu' jika tersedia (misalnya 'Ny. Olivia Chrisanti Tardianto').\n"
            . "7. Hapus semua sitasi [cite: X].\n\n"
            . "STRUKTUR JSON WAJIB:\n"
            . "{\n"
            . "  \"fasilitas_kesehatan\": {\n"
            . "    \"nama_rumah_sakit\": \"\",\n"
            . "    \"alamat_rs\": \"\",\n"
            . "    \"kontak\": {\n"
            . "      \"kantor\": \"\",\n"
            . "      \"igd\": \"\",\n"
            . "      \"email\": \"\"\n"
            . "    }\n"
            . "  },\n"
            . "  \"identitas_pasien\": {\n"
            . "    \"nomor_rm\": \"\",\n"
            . "    \"nama_pasien\": \"\",\n"
            . "    \"tanggal_lahir\": \"\",\n"
            . "    \"jenis_kelamin\": \"\",\n"
            . "    \"alamat_pasien\": \"\",\n"
            . "    \"nomor_telepon\": \"\"\n"
            . "  },\n"
            . "  \"data_kunjungan\": {\n"
            . "    \"tgl_masuk\": \"\",\n"
            . "    \"tgl_keluar\": \"\",\n"
            . "    \"lama_dirawat\": \"\",\n"
            . "    \"alasan_mrs\": \"\",\n"
            . "    \"diagnosis_utama\": \"\"\n"
            . "  },\n"
            . "  \"diagnosa_dan_tindakan\": {\n"
            . "    \"kode_icd_10\": \"\"\n"
            . "  },\n"
            . "  \"tenaga_medis\": {\n"
            . "    \"dokter_dpjp\": \"\",\n"
            . "    \"dokter_penolong_persalinan\": \"\",\n"
            . "    \"dokter_bidan_saksi_serah_terima\": \"\"\n"
            . "  },\n"
            . "  \"informasi_keluarga\": {\n"
            . "    \"wali_hukum_penanggung_jawab\": {\n"
            . "      \"nama\": \"\",\n"
            . "      \"hubungan\": \"\",\n"
            . "      \"alamat\": \"\",\n"
            . "      \"nomor_telepon\": \"\"\n"
            . "    },\n"
            . "    \"identitas_ibu\": {\n"
            . "      \"nama\": \"\"\n"
            . "    },\n"
            . "    \"penerima_wewenang_informasi\": []\n"
            . "  }\n"
            . "}\n\n"
            . "HANYA KEMBALIKAN JSON MURNI TANPA PENJELASAN.";

        $prompt = $systemInstruction . "\n\nEkstrak dokumen ini sekarang. Gabungkan informasi dari seluruh halaman untuk melengkapi field yang diminta secara akurat.";

        // Call Gemini Client
        $content = $this->geminiClient->visionExtract($images, $mimeType, $prompt, $systemInstruction);

        // Clean JSON from markdown and potential citations
        $content = $this->cleanAiResponse($content);

        $parsedData = json_decode($content, true);

        if (!$parsedData) {
            Log::warning("Gemini JSON Parse Failed. Raw content: " . substr($content, 0, 500));
            throw new \Exception("Gagal mendecode hasil AI dari Gemini. Pastikan dokumen terbaca jelas.");
        }

        return [
            'parsed_data' => $parsedData,
        ];
    }

    /**
     * Membersihkan response AI agar menjadi JSON murni yang valid
     */
    private function cleanAiResponse(string $content): string
    {
        // Cari blok JSON ```json ... ``` paling akhir
        if (preg_match_all('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = end($matches[1]);
        } else {
            // Jika tidak ada markdown, cari { ... }
            $start = strpos($content, '{');
            $end = strrpos($content, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $content = substr($content, $start, ($end - $start) + 1);
            }
        }
        
        // Hapus sitasi [cite: X]
        $content = preg_replace('/,\s*\[cite: \d+\]/i', ',', $content);
        $content = preg_replace('/\[cite: \d+\]/i', '', $content);
        
        return trim($content);
    }
}
