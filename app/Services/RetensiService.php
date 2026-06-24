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
        
        // Guard check: if already Dimusnahkan or Siap Dimusnahkan in DB, do not recalculate!
        $currentRetensi = $pasien->retensi ?? Retensi::where('no_rm', $pasien->no_rm)->first();
        if ($currentRetensi && in_array($currentRetensi->status, ['Dimusnahkan', 'Siap Dimusnahkan'])) {
            return null;
        }

        // Must have cases assigned to determine retention rules
        $kasus = $pasien->kasus;
        if (!$kasus) {
            return null;
        }

        $lastKunjungan = $pasien->kunjunganTerakhir ?? $pasien->kunjungan()->latest('tanggal_masuk')->first();
        if (!$lastKunjungan) {
            return null;
        }

        $masaAktif = $kasus->masa_retensi_aktif ?? 5;
        $masaInaktif = $kasus->masa_retensi_inaktif ?? 2;
        $status = $this->determineStatus($lastKunjungan->tanggal_masuk, $kasus);

        return Retensi::updateOrCreate(
            ['no_rm' => $pasien->no_rm],
            [
                'pasien_id' => $pasien->id,
                'kasus_id' => $pasien->kasus_id,
                'jenis_kasus_id' => $pasien->kasus_id,
                'tanggal_kunjungan_terakhir' => $lastKunjungan->tanggal_masuk,
                'status' => $status,
                'masa_aktif' => $masaAktif,
                'masa_inaktif' => $masaInaktif,
                'tanggal_batas_aktif' => Carbon::parse($lastKunjungan->tanggal_masuk)->addDays($masaAktif * 365),
                'tanggal_batas_musnah' => Carbon::parse($lastKunjungan->tanggal_masuk)->addDays(($masaAktif + $masaInaktif) * 365),
                'tanggal_proses' => Carbon::now(),
            ]
        );
    }

    /**
     * Determine status based on last visit and rules.
     */
    public function determineStatus($tanggalKunjungan, $kasus)
    {
        if (!$kasus) {
            return 'Aktif';
        }

        $today = Carbon::now()->startOfDay();
        $kunjungan = Carbon::parse($tanggalKunjungan)->startOfDay();
        $elapsedDays = (int) abs($today->diffInDays($kunjungan));

        $masaAktif = $kasus->masa_retensi_aktif ?? 5;
        $masaInaktif = $kasus->masa_retensi_inaktif ?? 2;

        $limitAktifDays = $masaAktif * 365;
        $limitTotalDays = ($masaAktif + $masaInaktif) * 365;

        if ($elapsedDays < $limitAktifDays) {
            return 'Aktif';
        } elseif ($elapsedDays < $limitTotalDays) {
            return 'Inaktif';
        } else {
            return 'Siap Dimusnahkan';
        }
    }
}
