<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pemusnahan extends Model {
    public const UPDATED_AT = null;
    protected $table = "daftar_pemusnahan";
    protected $guarded = [];

    public function pasien() { return $this->belongsTo(Pasien::class, "no_rm", "no_rm"); }
    public function beritaAcara() { return $this->hasOne(BeritaAcara::class, "id_pemusnahan", "id"); }
    public function destroyedBy() { return $this->belongsTo(User::class, "destroyed_by", "id"); }
    public function pengajuan() { return $this->belongsTo(PengajuanPemusnahan::class, "pengajuan_id"); }

    protected static function boot() {
        parent::boot();

        static::deleting(function($pemusnahan) {
            $pemusnahan->beritaAcara()->delete();
        });
    }
}