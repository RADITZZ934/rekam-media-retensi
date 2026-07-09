<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPemusnahan extends Model
{
    protected $table = 'pengajuan_pemusnahan';

    protected $fillable = [
        'no_sk',
        'tanggal_pengajuan',
        'ketua_tim',
        'anggota_tim_1',
        'anggota_tim_2',
        'anggota_tim_3',
        'anggota_tim_4',
        'anggota_tim_5',
        'anggota_tim_6',
        'anggota_tim_7',
        'anggota_tim_8',
        'jumlah_berkas',
        'status',
        'keterangan',
    ];

    /**
     * Get associated rekam medis items to be destroyed under this SK.
     */
    public function pemusnahan()
    {
        return $this->hasMany(Pemusnahan::class, 'pengajuan_id');
    }
}
