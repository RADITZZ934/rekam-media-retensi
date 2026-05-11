<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kasus extends Model
{
    use HasFactory;

    protected $table = 'kasus_master';
    public const UPDATED_AT = null;

    protected $fillable = [
        'kelompok',
        'jenis_kasus',
        'masa_aktif_rj',
        'masa_inaktif_rj',
        'masa_aktif_ri',
        'masa_inaktif_ri',
        'keterangan',
    ];

    protected $appends = [
        'kode_kasus',
        'nama_kasus',
        'kategori',
        'masa_retensi_aktif',
        'masa_retensi_inaktif',
        'status',
        'deskripsi',
    ];

    // Accessors ke frontend
    public function getKodeKasusAttribute() { return 'KAS' . str_pad($this->id, 3, '0', STR_PAD_LEFT); }
    public function getNamaKasusAttribute() { return $this->jenis_kasus; }
    public function getKategoriAttribute() { return $this->kelompok; }
    public function getMasaRetensiAktifAttribute() { return $this->masa_aktif_rj; }
    public function getMasaRetensiInaktifAttribute() { return $this->masa_inaktif_rj; }
    public function getStatusAttribute() { return 'Aktif'; }
    public function getDeskripsiAttribute() { return $this->keterangan; }

    /**
     * Scope untuk search
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where('jenis_kasus', 'like', "%{$keyword}%")
                     ->orWhere('id', 'like', "%{$keyword}%")
                     ->orWhere('kelompok', 'like', "%{$keyword}%");
    }
}
