<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ValidasiData extends Model {
    protected $table = "validasi_data";
    protected $guarded = [];
    public const UPDATED_AT = null;

    public function dokumen() { return $this->belongsTo(DokumenRekamMedis::class, "dokumen_id", "id"); }
    public function user() { return $this->belongsTo(User::class, "verified_by", "id"); }
}