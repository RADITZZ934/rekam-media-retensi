<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model {
    protected $table = "kunjungan";
    protected $primaryKey = "id_kunjungan";
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function pasien() { return $this->belongsTo(Pasien::class, "no_rm", "no_rm"); }
}