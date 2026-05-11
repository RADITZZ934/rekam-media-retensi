<?php

namespace App\Services;

use App\Models\Pasien;
use App\Models\Retensi;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RetensiService
{
    /**
     * Calculate retention for a specific patient.
     */
    public function calculateForPasien(Pasien $pasien)
    {
        Log::info("RetensiService: Calculating for Pasien " . $pasien->no_rm);
        // Must have cases assigned to determine retention rules
        $kasus = $pasien->kasus;
        if (!$kasus) {
            return null;
        }

        $lastKunjungan = $pasien->kunjungan()->latest('tanggal_masuk')->first();
        if (!$lastKunjungan) {
            return null;
        }

        $status = $this->determineStatus($lastKunjungan->tanggal_masuk, $kasus);

        $masaAktif = $kasus->masa_retensi_aktif ?? 5;
        $masaInaktif = $kasus->masa_retensi_inaktif ?? 2;

        return Retensi::updateOrCreate(
            ['no_rm' => $pasien->no_rm],
            [
                'pasien_id' => $pasien->id,
                'kasus_id' => $pasien->kasus_id,
                'jenis_kasus_id' => $pasien->kasus_id,
                'tanggal_kunjungan_terakhir' => $lastKunjungan->tanggal_masuk,
                'status_retensi' => $status,
                'masa_aktif' => $masaAktif,
                'masa_inaktif' => $masaInaktif,
                'tanggal_batas_aktif' => Carbon::parse($lastKunjungan->tanggal_masuk)->addYears($masaAktif),
                'tanggal_batas_musnah' => Carbon::parse($lastKunjungan->tanggal_masuk)->addYears($masaAktif + $masaInaktif),
                'tanggal_proses' => Carbon::now(),
            ]
        );
    }

    /**
     * Determine status based on last visit and rules.
     */
    public function determineStatus($tanggalKunjungan, $kasus)
    {
        if (!$kasus)
            return 'Aktif';

        $today = Carbon::now();
        $masaAktif = $kasus->masa_retensi_aktif ?? 5;
        $masaInaktif = $kasus->masa_retensi_inaktif ?? 2;

        $batasAktif = Carbon::parse($tanggalKunjungan)->addYears($masaAktif);
        $batasMusnah = $batasAktif->copy()->addYears($masaInaktif);

        if ($today < $batasAktif) {
            return 'Aktif';
        } elseif ($today < $batasMusnah) {
            return 'Inaktif';
        } else {
            return 'Siap Musnah';
        }
    }
}
