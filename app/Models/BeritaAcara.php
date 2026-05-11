<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model {
    protected $table = "berita_acara_pemusnahan";
    protected $guarded = [];

    public function pemusnahan() { return $this->belongsTo(Pemusnahan::class, "id_pemusnahan", "id"); }
}