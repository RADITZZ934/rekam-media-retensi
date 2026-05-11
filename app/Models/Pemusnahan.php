<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pemusnahan extends Model {
    protected $table = "daftar_pemusnahan";
    protected $guarded = [];

    public function pasien() { return $this->belongsTo(Pasien::class, "no_rm", "no_rm"); }
}