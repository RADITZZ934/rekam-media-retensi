<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DokumenRekamMedis extends Model {
    protected $table = "dokumen_rekam_medis";
    protected $guarded = [];
    public const UPDATED_AT = null;

    public function pasien() { return $this->belongsTo(Pasien::class, "no_rm", "no_rm"); }
    public function ocrResult() { return $this->hasOne(OCRResult::class, "dokumen_id", "id"); }
    public function user() { return $this->belongsTo(User::class, "user_id"); }
}