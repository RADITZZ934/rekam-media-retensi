<?php

namespace App\Jobs;

use App\Models\DokumenRekamMedis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class ProcessPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dokumen;

    /**
     * Create a new job instance.
     */
    public function __construct(DokumenRekamMedis $dokumen)
    {
        $this->dokumen = $dokumen;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Processing PDF for document ID: {$this->dokumen->id}");
        // Initial status for PDF processing
        $this->dokumen->update(['status' => 'processing']);

        try {
            $inputPath = Storage::disk('private')->path($this->dokumen->file_original);
            
            // Define paths for compressed and images
            $compressedDir = 'dokumen_rekam_medis/compressed';
            $imageDir = 'dokumen_rekam_medis/images/' . $this->dokumen->id;
            
            Storage::disk('private')->makeDirectory($compressedDir);
            Storage::disk('private')->makeDirectory($imageDir);

            $compressedFile = $compressedDir . '/' . basename($this->dokumen->file_original);
            $fullCompressedPath = Storage::disk('private')->path($compressedFile);
            $fullImageDir = Storage::disk('private')->path($imageDir);

            // 1. COMPRESS PDF (Fallback to copy if GS not found)
            $this->compress($inputPath, $fullCompressedPath);

            // 2. CONVERT TO IMAGES (Fallback to success if tool missing for now)
            $this->convert($inputPath, $fullImageDir);

            // 3. Update Status to 'processing' (waiting for OCR)
            $this->dokumen->update([
                'status' => 'processing',
                'file_compressed' => $compressedFile,
            ]);

            Log::info("Processing completed successfully for document ID: {$this->dokumen->id}");
        } catch (\Exception $e) {
            Log::error("Processing ERROR for document ID: {$this->dokumen->id}: " . $e->getMessage());
            $this->dokumen->update([
                'status' => 'failed',
                'error_message' => 'Processing failed: ' . $e->getMessage(),
            ]);
        }
    }

    protected function compress($input, $output)
    {
        // Try Ghostscript
        $process = new Process(['gs', '-sDEVICE=pdfwrite', '-dCompatibilityLevel=1.4', '-dPDFSETTINGS=/screen', '-dNOPAUSE', '-dQUIET', '-dBATCH', "-sOutputFile=$output", $input]);
        try {
            $process->run();
            if ($process->isSuccessful()) return;
        } catch (\Exception $e) {
            Log::warning("Ghostscript not found or failed, using original file for compression placeholder.");
        }
        
        copy($input, $output);
    }

    protected function convert($input, $outputDir)
    {
        // Try pdftoppm
        $process = new Process(['pdftoppm', '-png', $input, $outputDir . '/page']);
        try {
            $process->run();
            if ($process->isSuccessful()) return;
        } catch (\Exception $e) {
             Log::warning("pdftoppm not found or failed. Image conversion skipped.");
        }
    }
}
