<?php

namespace App\Jobs;

use App\Models\DokumenRekamMedis;
use App\Services\OCRService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOCRJob implements ShouldQueue
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
    public function handle(OCRService $ocrService): void
    {
        // Set processing status
        $this->dokumen->update(['status' => 'processing']);
        
        // Let OCR Service handle compress, convert, parse, insert to ocr_result
        $ocrService->processDocument($this->dokumen);
    }
}
