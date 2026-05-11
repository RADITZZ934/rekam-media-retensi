<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OCRResult extends Model {
    protected $table = "ocr_result";
    protected $guarded = [];

    protected $casts = [
        'parsed_data' => 'array',
        'ai_result' => 'array',
    ];

    public function dokumen() { return $this->belongsTo(DokumenRekamMedis::class, "dokumen_id", "id"); }
}