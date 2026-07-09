<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DokumenRekamMedis extends Model {
    protected $table = "dokumen_rekam_medis";
    protected $guarded = [];
    public const UPDATED_AT = null;

    public function pasien() { return $this->belongsTo(Pasien::class, "no_rm", "no_rm"); }
    public function ocrResult() { return $this->hasOne(OCRResult::class, "dokumen_id", "id"); }
    public function validasiData() { return $this->hasOne(ValidasiData::class, "dokumen_id", "id"); }
    public function user() { return $this->belongsTo(User::class, "user_id"); }

    protected static function boot() {
        parent::boot();

        static::deleting(function($dokumen) {
            $dokumen->ocrResult()->delete();
            $dokumen->validasiData()->delete();

            // Clean up physical files from disk
            foreach (['nama_file', 'file_original', 'file_compressed'] as $field) {
                if ($dokumen->$field) {
                    $path1 = public_path('storage/alih-media/' . basename($dokumen->$field));
                    if (file_exists($path1)) {
                        @unlink($path1);
                    }
                    $path2 = public_path('storage/alih_media/' . basename($dokumen->$field));
                    if (file_exists($path2)) {
                        @unlink($path2);
                    }
                }
            }
        });
    }
}