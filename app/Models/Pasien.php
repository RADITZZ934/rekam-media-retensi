<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasien';
    protected $primaryKey = 'no_rm';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rm',
        'nama_pasien',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'alamat',
        'no_telepon',
        'status_rm',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship dengan Kunjungan
     */
    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'no_rm', 'no_rm');
    }

    /**
     * Relationship dengan Retensi
     */
    public function retensi()
    {
        return $this->hasOne(Retensi::class, 'no_rm', 'no_rm');
    }

    /**
     * Get kunjungan terakhir
     */
    public function kunjunganTerakhir()
    {
        return $this->hasOne(Kunjungan::class, 'no_rm', 'no_rm')->latestOfMany();
    }

    /**
     * Scope untuk filter status RM
     */
    public function scopeStatusRm($query, $status)
    {
        return $query->where('status_rm', $status);
    }

    /**
     * Scope untuk search
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where('nama_pasien', 'like', "%{$keyword}%")
                     ->orWhere('no_rm', 'like', "%{$keyword}%");
    }
}
