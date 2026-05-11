<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pasien;
use App\Models\Kunjungan;
use App\Models\Retensi;
use App\Models\Kasus;
use Carbon\Carbon;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Kasus untuk reference
        $kasusHipertensi = Kasus::where('kode_kasus', 'KAS001')->first();
        $kasusDiabetes = Kasus::where('kode_kasus', 'KAS002')->first();
        $kasusPneumonia = Kasus::where('kode_kasus', 'KAS003')->first();
        $kasusTB = Kasus::where('kode_kasus', 'KAS004')->first();
        $kasusKanker = Kasus::where('kode_kasus', 'KAS005')->first();

        // Data pasien untuk test
        $pasienData = [
            [
                'no_rm' => 'RM00001001',
                'nama_pasien' => 'Ahmad Satriawan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1985-12-05',
                'tempat_lahir' => 'Jember',
                'alamat' => 'Jl. Jember No. 123',
                'no_telepon' => '08124567890',
                'status_rm' => 'Aktif',
                'kasus_id' => $kasusHipertensi?->id,
                'tgl_kunjungan' => '2026-03-11',
            ],
            [
                'no_rm' => 'RM00001002',
                'nama_pasien' => 'Siti Aminah',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1992-07-21',
                'tempat_lahir' => 'Surabaya',
                'alamat' => 'Jl. Banyuwangi No. 456',
                'no_telepon' => '08124567891',
                'status_rm' => 'Aktif',
                'kasus_id' => $kasusDiabetes?->id,
                'tgl_kunjungan' => '2018-02-10',
            ],
            [
                'no_rm' => 'RM00001003',
                'nama_pasien' => 'Budi Santoso',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1988-03-15',
                'tempat_lahir' => 'Malang',
                'alamat' => 'Jl. Malang No. 759',
                'no_telepon' => '08124567892',
                'status_rm' => 'Aktif',
                'kasus_id' => $kasusPneumonia?->id,
                'tgl_kunjungan' => '2026-02-28',
            ],
            [
                'no_rm' => 'RM00001004',
                'nama_pasien' => 'Dewi Sartika',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1995-06-11',
                'tempat_lahir' => 'Bandung',
                'alamat' => 'Jl. Surabaya No. 321',
                'no_telepon' => '08124567893',
                'status_rm' => 'Aktif',
                'kasus_id' => $kasusTB?->id,
                'tgl_kunjungan' => '2026-03-15',
            ],
            [
                'no_rm' => 'RM00001005',
                'nama_pasien' => 'Rudi Hermawan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1983-07-23',
                'tempat_lahir' => 'Kebumen',
                'alamat' => 'Jl. Probilinggo No. 654',
                'no_telepon' => '08124567894',
                'status_rm' => 'Inaktif',
                'kasus_id' => $kasusKanker?->id,
                'tgl_kunjungan' => '2020-01-05',
            ],
        ];

        // Insert pasien
        foreach ($pasienData as $data) {
            $tglKunjungan = $data['tgl_kunjungan'] ?? now();
            $kasusId = $data['kasus_id'];
            
            $pasienCreate = $data;
            unset($pasienCreate['tgl_kunjungan']);
            
            $pasien = Pasien::create($pasienCreate);

            // Create kunjungan terakhir
            Kunjungan::create([
                'no_rm' => $pasien->no_rm,
                'tanggal_masuk' => $tglKunjungan,
                'tanggal_keluar' => $tglKunjungan,
                'diagnosis' => 'Diagnosa',
                'keterangan' => 'Keterangan kunjungan',
            ]);

            // Calculate retensi dates berdasarkan Kasus
            $kasus = $kasusId ? Kasus::find($kasusId) : null;
            $tglKunjunganCarbon = Carbon::parse($tglKunjungan);
            
            // Default jika tidak ada Kasus
            $masaAktif = 5;
            $masaInaktif = 2;
            
            if ($kasus) {
                $masaAktif = $kasus->masa_retensi_aktif;
                $masaInaktif = $kasus->masa_retensi_inaktif;
            }
            
            $tglBatasAktif = $tglKunjunganCarbon->copy()->addYears($masaAktif);
            $tglBatasMusnah = $tglKunjunganCarbon->copy()->addYears($masaAktif + $masaInaktif);
            
            // Determine status
            $now = Carbon::now();
            if ($now < $tglBatasAktif) {
                $statusRetensi = 'Aktif';
            } elseif ($now < $tglBatasMusnah) {
                $statusRetensi = 'Inaktif';
            } else {
                $statusRetensi = 'Siap Musnah';
            }

            // Create retensi dengan calculated dates
            Retensi::create([
                'no_rm' => $pasien->no_rm,
                'kasus_id' => $kasusId,
                'status_retensi' => $statusRetensi,
                'tanggal_mulai_retensi' => $tglKunjunganCarbon,
                'tanggal_batas_aktif' => $tglBatasAktif,
                'tanggal_batas_musnah' => $tglBatasMusnah,
                'keterangan' => $kasus ? "Retensi berdasarkan kasus: {$kasus->nama_kasus}" : 'Retensi default',
            ]);
        }
    }
}

