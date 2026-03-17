<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pasien;
use App\Models\Kunjungan;
use App\Models\Retensi;
use Carbon\Carbon;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            ],
        ];

        // Insert pasien
        foreach ($pasienData as $data) {
            $pasien = Pasien::create($data);

            // Create kunjungan terakhir
            $tglKunjungan = match ($data['no_rm']) {
                'RM00001001' => '2026-03-11',
                'RM00001002' => '2018-02-10',
                'RM00001003' => '2026-02-28',
                'RM00001004' => '2026-03-15',
                'RM00001005' => '2020-01-05',
                default => now()->format('Y-m-d'),
            };

            Kunjungan::create([
                'no_rm' => $pasien->no_rm,
                'tanggal_masuk' => $tglKunjungan,
                'tanggal_keluar' => $tglKunjungan,
                'diagnosis' => 'Diagnosa',
                'keterangan' => 'Keterangan kunjungan',
            ]);

            // Create retensi
            $statusRetensi = match ($data['no_rm']) {
                'RM00001001' => 'Aktif',
                'RM00001002' => 'Siap Musnah',
                'RM00001003' => 'Aktif',
                'RM00001004' => 'Aktif',
                'RM00001005' => 'Inaktif',
                default => 'Aktif',
            };

            Retensi::create([
                'no_rm' => $pasien->no_rm,
                'status_retensi' => $statusRetensi,
                'tanggal_mulai_retensi' => Carbon::now()->subYear(),
                'tanggal_akhir_retensi' => $statusRetensi === 'Siap Musnah' ? Carbon::now() : null,
                'keterangan' => 'Retensi pasien',
            ]);
        }
    }
}

