<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kasus;

class KasusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kasusList = [
            [
                'kode_kasus' => 'KAS001',
                'nama_kasus' => 'Hipertensi',
                'deskripsi' => 'Tekanan darah tinggi',
                'kategori' => 'Kardiovaskular',
                'masa_retensi_aktif' => 5,
                'masa_retensi_inaktif' => 2,
                'status' => 'Aktif',
            ],
            [
                'kode_kasus' => 'KAS002',
                'nama_kasus' => 'Diabetes Mellitus',
                'deskripsi' => 'Penyakit metabolik',
                'kategori' => 'Endokrinologi',
                'masa_retensi_aktif' => 5,
                'masa_retensi_inaktif' => 3,
                'status' => 'Aktif',
            ],
            [
                'kode_kasus' => 'KAS003',
                'nama_kasus' => 'Pneumonia',
                'deskripsi' => 'Infeksi paru-paru',
                'kategori' => 'Respirasi',
                'masa_retensi_aktif' => 3,
                'masa_retensi_inaktif' => 2,
                'status' => 'Aktif',
            ],
            [
                'kode_kasus' => 'KAS004',
                'nama_kasus' => 'Tuberkulosis Paru',
                'deskripsi' => 'Infeksi Mycobacterium tuberculosis',
                'kategori' => 'Respirasi',
                'masa_retensi_aktif' => 10,
                'masa_retensi_inaktif' => 5,
                'status' => 'Aktif',
            ],
            [
                'kode_kasus' => 'KAS005',
                'nama_kasus' => 'Kanker Paru',
                'deskripsi' => 'Malignancy paru-paru',
                'kategori' => 'Onkologi',
                'masa_retensi_aktif' => 10,
                'masa_retensi_inaktif' => 10,
                'status' => 'Aktif',
            ],
            [
                'kode_kasus' => 'KAS006',
                'nama_kasus' => 'Kolitis Ulseratif',
                'deskripsi' => 'Inflamasi usus besar',
                'kategori' => 'Gastroenterologi',
                'masa_retensi_aktif' => 5,
                'masa_retensi_inaktif' => 3,
                'status' => 'Nonaktif',
            ],
        ];

        foreach ($kasusList as $data) {
            Kasus::create([
                'jenis_kasus' => $data['nama_kasus'],
                'keterangan' => $data['deskripsi'],
                'kelompok' => $data['kategori'],
                'masa_aktif_rj' => $data['masa_retensi_aktif'],
                'masa_inaktif_rj' => $data['masa_retensi_inaktif'],
                'masa_aktif_ri' => $data['masa_retensi_aktif'],
                'masa_inaktif_ri' => $data['masa_retensi_inaktif'],
            ]);
        }
    }
}

