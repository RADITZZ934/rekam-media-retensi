<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kasus extends Model
{
    use HasFactory;

    protected $table = 'kasus_master';

    protected $fillable = [
        'kode_kasus',
        'nama_kasus',
        'deskripsi',
        'kategori',
        'masa_retensi_aktif',
        'masa_retensi_inaktif',
        'status',
    ];

    /**
     * Scope untuk filter status
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Scope untuk search
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where('nama_kasus', 'like', "%{$keyword}%")
                     ->orWhere('kode_kasus', 'like', "%{$keyword}%")
                     ->orWhere('kategori', 'like', "%{$keyword}%");
    }
}
