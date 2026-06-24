<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pasien;
use App\Models\Kunjungan;
use App\Models\Retensi;
use App\Models\Kasus;
use App\Models\Pemusnahan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DummyRetensiTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Kasus Hipertensi or first available case as reference
        $kasus = Kasus::where('jenis_kasus', 'like', '%Hipertensi%')->first() ?? Kasus::first();
        if (!$kasus) {
            $kasus = Kasus::create([
                'kelompok' => 'Kardiovaskular',
                'jenis_kasus' => 'Hipertensi',
                'masa_aktif_rj' => 5,
                'masa_inaktif_rj' => 2,
                'masa_aktif_ri' => 5,
                'masa_inaktif_ri' => 2,
                'keterangan' => 'Tekanan darah tinggi',
            ]);
        }

        // 1. Clear all old test data
        $dummyRMs = [];
        for ($i = 1; $i <= 100; $i++) {
            $dummyRMs[] = 'RM_TEST_' . str_pad($i, 3, '0', STR_PAD_LEFT);
        }

        Pemusnahan::whereIn('no_rm', $dummyRMs)->delete();
        Retensi::whereIn('no_rm', $dummyRMs)->delete();
        Kunjungan::whereIn('no_rm', $dummyRMs)->delete();
        Pasien::whereIn('no_rm', $dummyRMs)->delete();

        // 2. Create 8 dummy data items with different test scenarios

        // --- SCENARIO 1: Tetap/Menjadi Aktif (RM_TEST_001 & RM_TEST_002)
        // Kunjungan terakhir 2 tahun lalu. Batas aktif 5 tahun, so it's currently active.
        $tgl1 = Carbon::now()->subYears(2);
        for ($i = 1; $i <= 2; $i++) {
            $no_rm = 'RM_TEST_' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $pasien = Pasien::create([
                'no_rm' => $no_rm,
                'nama_pasien' => "Pasien Dummy Aktif {$i}",
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1990-01-01',
                'alamat' => 'Alamat Dummy',
                'status_rm' => 'Aktif',
                'kasus_id' => $kasus->id,
            ]);
            Kunjungan::create([
                'no_rm' => $no_rm,
                'tanggal_masuk' => $tgl1,
                'tanggal_keluar' => $tgl1,
                'diagnosa' => 'Diagnosa Dummy',
            ]);
            // Set initial status to Inaktif to test transition back to Aktif
            Retensi::create([
                'no_rm' => $no_rm,
                'jenis_kasus_id' => $kasus->id,
                'tanggal_kunjungan_terakhir' => $tgl1,
                'status' => 'Inaktif',
                'masa_aktif' => 5,
                'masa_inaktif' => 2,
                'tanggal_batas_aktif' => $tgl1->copy()->addYears(5),
                'tanggal_batas_musnah' => $tgl1->copy()->addYears(7),
                'tanggal_proses' => Carbon::now(),
            ]);
        }

        // --- SCENARIO 2: Menjadi Inaktif (RM_TEST_003 & RM_TEST_004)
        // Kunjungan terakhir 6 tahun lalu. Masa aktif 5 tahun lewat, but within 7 years total.
        $tgl2 = Carbon::now()->subYears(6);
        for ($i = 3; $i <= 4; $i++) {
            $no_rm = 'RM_TEST_' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $pasien = Pasien::create([
                'no_rm' => $no_rm,
                'nama_pasien' => "Pasien Dummy Inaktif {$i}",
                'jenis_kelamin' => 'Perempuan',
                'tanggal_lahir' => '1992-02-02',
                'alamat' => 'Alamat Dummy',
                'status_rm' => 'Aktif',
                'kasus_id' => $kasus->id,
            ]);
            Kunjungan::create([
                'no_rm' => $no_rm,
                'tanggal_masuk' => $tgl2,
                'tanggal_keluar' => $tgl2,
                'diagnosa' => 'Diagnosa Dummy',
            ]);
            // Set initial status to Aktif to test transition to Inaktif
            Retensi::create([
                'no_rm' => $no_rm,
                'jenis_kasus_id' => $kasus->id,
                'tanggal_kunjungan_terakhir' => $tgl2,
                'status' => 'Aktif',
                'masa_aktif' => 5,
                'masa_inaktif' => 2,
                'tanggal_batas_aktif' => $tgl2->copy()->addYears(5),
                'tanggal_batas_musnah' => $tgl2->copy()->addYears(7),
                'tanggal_proses' => Carbon::now(),
            ]);
        }

        // --- SCENARIO 3: Menjadi Siap Dimusnahkan (RM_TEST_005 & RM_TEST_006)
        // Kunjungan terakhir 8 tahun lalu. Masa aktif 5 tahun + inaktif 2 tahun = 7 tahun sudah lewat.
        $tgl3 = Carbon::now()->subYears(8);
        for ($i = 5; $i <= 6; $i++) {
            $no_rm = 'RM_TEST_' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $pasien = Pasien::create([
                'no_rm' => $no_rm,
                'nama_pasien' => "Pasien Dummy Siap Musnah {$i}",
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1988-03-03',
                'alamat' => 'Alamat Dummy',
                'status_rm' => 'Aktif',
                'kasus_id' => $kasus->id,
            ]);
            Kunjungan::create([
                'no_rm' => $no_rm,
                'tanggal_masuk' => $tgl3,
                'tanggal_keluar' => $tgl3,
                'diagnosa' => 'Diagnosa Dummy',
            ]);
            // Set initial status to Inaktif to test transition to Siap Dimusnahkan
            Retensi::create([
                'no_rm' => $no_rm,
                'jenis_kasus_id' => $kasus->id,
                'tanggal_kunjungan_terakhir' => $tgl3,
                'status' => 'Inaktif',
                'masa_aktif' => 5,
                'masa_inaktif' => 2,
                'tanggal_batas_aktif' => $tgl3->copy()->addYears(5),
                'tanggal_batas_musnah' => $tgl3->copy()->addYears(7),
                'tanggal_proses' => Carbon::now(),
            ]);
        }

        // --- SCENARIO 4: Status Dimusnahkan / Guard (RM_TEST_007 & RM_TEST_008)
        // Kunjungan terakhir 8 tahun lalu. Tetapi status diset 'Dimusnahkan' dan terdaftar di Pemusnahan.
        $tgl4 = Carbon::now()->subYears(8);
        for ($i = 7; $i <= 8; $i++) {
            $no_rm = 'RM_TEST_' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $pasien = Pasien::create([
                'no_rm' => $no_rm,
                'nama_pasien' => "Pasien Dummy Dimusnahkan {$i}",
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1985-04-04',
                'alamat' => 'Alamat Dummy',
                'status_rm' => 'Inaktif',
                'kasus_id' => $kasus->id,
            ]);
            Kunjungan::create([
                'no_rm' => $no_rm,
                'tanggal_masuk' => $tgl4,
                'tanggal_keluar' => $tgl4,
                'diagnosa' => 'Diagnosa Dummy',
            ]);
            Retensi::create([
                'no_rm' => $no_rm,
                'jenis_kasus_id' => $kasus->id,
                'tanggal_kunjungan_terakhir' => $tgl4,
                'status' => 'Dimusnahkan',
                'masa_aktif' => 5,
                'masa_inaktif' => 2,
                'tanggal_batas_aktif' => $tgl4->copy()->addYears(5),
                'tanggal_batas_musnah' => $tgl4->copy()->addYears(7),
                'tanggal_proses' => Carbon::now(),
            ]);
            Pemusnahan::create([
                'no_rm' => $no_rm,
                'tanggal_retensi' => Carbon::now()->subDays(2),
                'status' => 'dimusnahkan',
                'tanggal_pemusnahan' => Carbon::now()->subDays(1),
            ]);
        }

        $this->command->info('8 dummy data items created successfully.');
    }
}
