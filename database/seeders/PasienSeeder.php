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
        // Clear old records
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Retensi::truncate();
        Kunjungan::truncate();
        Pasien::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Get Kasus untuk reference
        $kasusHipertensi = Kasus::where('jenis_kasus', 'UMUM')->first();
        $kasusDiabetes = Kasus::where('jenis_kasus', 'MATA')->first();
        $kasusPneumonia = Kasus::where('jenis_kasus', 'JIWA')->first();
        $kasusTB = Kasus::where('jenis_kasus', 'ORTHOPAEDI')->first();
        $kasusKanker = Kasus::where('jenis_kasus', 'KUSTA')->first();

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
            if (($pasienCreate['jenis_kelamin'] ?? null) === 'L') {
                $pasienCreate['jenis_kelamin'] = 'Laki-laki';
            } elseif (($pasienCreate['jenis_kelamin'] ?? null) === 'P') {
                $pasienCreate['jenis_kelamin'] = 'Perempuan';
            }
            
            $pasien = Pasien::create($pasienCreate);

            Kunjungan::create([
                'no_rm' => $pasien->no_rm,
                'tanggal_masuk' => $tglKunjungan,
                'tanggal_keluar' => $tglKunjungan,
                'diagnosa' => 'Diagnosa',
            ]);

            // Calculate retensi using RetensiService
            $retensiService = app(\App\Services\RetensiService::class);
            $retensiService->calculateForPasien($pasien);
        }
    }
}

