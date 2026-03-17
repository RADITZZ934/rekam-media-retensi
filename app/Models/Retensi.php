<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Retensi extends Model
{
    use HasFactory;

    protected $table = 'retensi';

    protected $fillable = [
        'no_rm',
        'status_retensi',
        'tanggal_mulai_retensi',
        'tanggal_akhir_retensi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai_retensi' => 'date',
        'tanggal_akhir_retensi' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship dengan Pasien
     */
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rm', 'no_rm');
    }
}
