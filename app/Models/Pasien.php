<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model {
    protected $table = "pasien";
    protected $primaryKey = "no_rm";
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
    public $timestamps = true;

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function kasus() { return $this->belongsTo(Kasus::class, "kasus_id", "id"); }
    public function kunjungan() { return $this->hasMany(Kunjungan::class, "no_rm", "no_rm"); }
    public function kunjunganTerakhir() { return $this->hasOne(Kunjungan::class, "no_rm", "no_rm")->latestOfMany('tanggal_masuk'); }
    public function retensi() { return $this->hasOne(Retensi::class, "no_rm", "no_rm"); }
    public function dokumen() { return $this->hasMany(DokumenRekamMedis::class, "no_rm", "no_rm"); }
    public function pemusnahan() { return $this->hasMany(Pemusnahan::class, "no_rm", "no_rm"); }

    public function scopeSearch($query, $search)
    {
        return $query->where('no_rm', 'like', "%{$search}%")
                     ->orWhere('nama_pasien', 'like', "%{$search}%");
    }

    public function scopeStatusRm($query, $status)
    {
        return $query->where('status_rm', $status);
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($pasien) {
            // Delete related retensi
            $pasien->retensi()?->delete();
            
            // Delete related pemusnahan (deletes berita acara automatically)
            foreach ($pasien->pemusnahan as $pemusnahan) {
                $pemusnahan->delete();
            }
            
            // Delete related kunjungan
            $pasien->kunjungan()->delete();
            
            // Delete related dokumen (deletes OCR, validations, and files automatically)
            foreach ($pasien->dokumen as $doc) {
                $doc->delete();
            }
        });
    }
}