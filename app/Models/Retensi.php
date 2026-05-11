<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Retensi extends Model {
    protected $table = "retensi";
    protected $guarded = [];

    protected $casts = [
        'tanggal_kunjungan_terakhir' => 'date',
        'tanggal_proses' => 'datetime',
        'tanggal_batas_aktif' => 'date',
        'tanggal_batas_musnah' => 'date',
    ];

    public function pasien() { return $this->belongsTo(Pasien::class, "no_rm", "no_rm"); }
    public function kasus() { return $this->belongsTo(Kasus::class, "jenis_kasus_id", "id"); }
    public function kunjungan() { return $this->hasMany(Kunjungan::class, "no_rm", "no_rm"); }
}