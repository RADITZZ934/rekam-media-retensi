<?php

namespace App\Services;

use App\Models\Retensi;
use App\Models\Pasien;
use App\Models\KasusMaster;
use Carbon\Carbon;

class RetentionService
{
    /**
     * Hitung status retensi pasien
     */
    public function calculateForPasien(Pasien $pasien)
    {
        // 1. Ambil kunjungan terakhir
        $kunjunganTerakhir = $pasien->kunjungan()->latest('tanggal_masuk')->first();
        if (!$kunjunganTerakhir) {
            return false;
        }
        
        // 2. Tentukan kasus master
        // Asumsi tipe layanan rawat jalan (RJ) / rawat inap (RI) didapat dari kunjungan, kita set default RJ jika nihil
        $jenisLayanan = 'RJ'; 
        // Menggunakan foreign key dari kasus_master table jika dimapping ke kunjungan, atau global dari pasien.
        // Jika tidak ada di tabel terkait, cari kasus_master ID 1
        $kasus_id = 1; // Default
        
        $kasusMaster = KasusMaster::where('id', $kasus_id)->first();
        if (!$kasusMaster) {
            return false;
        }

        // 3. Hitung masa aktif dan inaktif
        $masaAktif = $jenisLayanan === 'RJ' ? $kasusMaster->masa_aktif_rj : $kasusMaster->masa_aktif_ri;
        $masaInaktif = $jenisLayanan === 'RJ' ? $kasusMaster->masa_inaktif_rj : $kasusMaster->masa_inaktif_ri;
        
        $today = Carbon::now();
        $tanggalTerakhir = Carbon::parse($kunjunganTerakhir->tanggal_masuk);
        $selisihTahun = $today->diffInYears($tanggalTerakhir);
        
        $batasAktif = $tanggalTerakhir->copy()->addYears($masaAktif);
        $batasMusnah = $batasAktif->copy()->addYears($masaInaktif);
        
        $status = 'Aktif';
        if ($today > $batasMusnah) {
            $status = 'Siap Musnah';
        } elseif ($today > $batasAktif) {
            $status = 'Inaktif';
        }
        
        // 4. Update tabel Retensi
        $retensi = Retensi::updateOrCreate(
            ['no_rm' => $pasien->no_rm],
            [
                'jenis_kasus_id' => $kasusMaster->id,
                'jenis_layanan' => $jenisLayanan,
                'tanggal_kunjungan_terakhir' => $tanggalTerakhir,
                'masa_aktif' => $masaAktif,
                'masa_inaktif' => $masaInaktif,
                'selisih_tahun' => $selisihTahun,
                'status_retensi' => $status,
                'tanggal_proses' => now(),
            ]
        );
        
        return $retensi;
    }
}
