<?php

namespace App\Services;

use App\Models\DokumenRekamMedis;
use App\Models\OCRResult;
use Illuminate\Support\Facades\Log;
use Throwable;

class OCRService
{
    protected YuulabsClient $yuulabsClient;

    public function __construct(YuulabsClient $yuulabsClient)
    {
        $this->yuulabsClient = $yuulabsClient;
    }

    /**
     * Proses OCR Utama dengan konsep "Background AI Chat" (via ChatGPT Vision Proxy)
     */
    public function processDocument(DokumenRekamMedis $dokumen)
    {
        try {
            $dokumen->update(['status' => 'processing', 'error_message' => null]);

            // Eksekusi ChatGPT Vision via Yuulabs Proxy
            $result = $this->callChatgptVisionProxy($dokumen);

            // Simpan hasil OCR ke ocr_result
            $dokumen->ocrResult()->updateOrCreate(
                ['dokumen_id' => $dokumen->id],
                [
                    'ocr_text' => json_encode($result['parsed_data'], JSON_PRETTY_PRINT),
                    'ai_result' => json_encode($result['parsed_data']),
                    'parsed_data' => json_encode($result['parsed_data']),
                    'engine' => 'yuulabs-chatgpt-vision',
                    'status' => 'success',
                ]
            );

            // Update dokumen status = success
            $dokumen->update([
                'status' => 'success',
                'engine' => 'chatgpt',
                'error_message' => null,
            ]);

            return $result['parsed_data'];

        } catch (Throwable $e) {
            Log::error("OCR Background Process Failed (ChatGPT): " . $e->getMessage());
            $dokumen->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Komunikasi dengan ChatGPT Vision melalui Yuulabs Proxy
     */
    private function callChatgptVisionProxy(DokumenRekamMedis $dokumen): array
    {
        $path = storage_path('app/private/' . ($dokumen->file_compressed ?? $dokumen->file_original));
        if (!file_exists($path)) {
            throw new \Exception("File dokumen tidak ditemukan.");
        }

        $systemInstruction = "Tugasmu adalah mengekstrak data rekam medis dari gambar menjadi JSON murni secara sangat teliti.\n\n"
            . "INSTRUKSI KHUSUS:\n"
            . "1. Cari JENIS KELAMIN dan ALAMAT di seluruh bagian dokumen. Jika di kolom identitas kosong, cari di bagian narasi, header, atau data wali/orang tua.\n"
            . "2. Untuk bayi, alamat biasanya mengikuti alamat Ibu atau Wali. Ambil dari sana jika alamat pasien tidak tertulis eksplisit.\n"
            . "3. Ekstrak nama Ibu di bagian 'identitas_ibu' jika tersedia.\n"
            . "4. Hapus semua sitasi [cite: X].\n\n"
            . "STRUKTUR JSON WAJIB:\n"
            . "{\n"
            . "  'fasilitas_kesehatan': { 'nama_rs': '', 'alamat': '', 'kontak': { 'kantor': '', 'igd': '', 'email': '' } },\n"
            . "  'identitas_pasien': { 'nama_pasien': '', 'nomor_rekam_medis': '', 'tanggal_lahir': '', 'jenis_kelamin': '', 'alamat': '', 'nomor_telepon': '' },\n"
            . "  'data_kunjungan': { 'tanggal_masuk': '', 'tanggal_keluar': '', 'jumlah_lama_dirawat': '', 'alasan_mrs': '', 'berat_badan_lahir': '', 'panjang_badan': '' },\n"
            . "  'diagnosa_dan_tindakan': { 'diagnosa_utama': '', 'kode_icd_10': '' },\n"
            . "  'tenaga_medis': { 'dokter_penanggung_jawab_dpjp': '', 'dokter_penolong_persalinan': '', 'dokter_bidan_saksi_serah_terima': '' },\n"
            . "  'informasi_keluarga': { 'wali_hukum_penanggung_jawab': { 'nama': '', 'hubungan': '', 'alamat': '', 'nomor_telepon': '' }, 'identitas_ibu': { 'nama': '' }, 'penerima_wewenang_informasi': [] }\n"
            . "}\n\n"
            . "HANYA KEMBALIKAN JSON MURNI TANPA PENJELASAN.";

        $prompt = $systemInstruction . "\n\nEkstrak dokumen ini sekarang.";

        $content = $this->yuulabsClient->chatgptVision($path, $prompt, 'gpt-4o');

        // Clean JSON from markdown and potential citations
        $content = $this->cleanAiResponse($content);

        $parsedData = json_decode($content, true);

        if (!$parsedData) {
            Log::warning("ChatGPT JSON Parse Failed. Raw content: " . substr($content, 0, 500));
            throw new \Exception("Gagal mendecode hasil AI dari ChatGPT. Pastikan dokumen terbaca jelas.");
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
            // Jika tidak ada markdown, cari { ... } paling akhir
            $start = strrpos($content, '{');
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
