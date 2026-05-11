<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('nama_lengkap', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('role', ['Administrator', 'Staff']);
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->dateTime('last_login')->nullable();
            $table->timestamps();
        });

        // 2. pasien
        Schema::create('pasien', function (Blueprint $table) {
            $table->string('no_rm', 20)->primary();
            $table->string('nama_pasien', 100)->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->enum('status_rm', ['Aktif', 'Inaktif'])->default('Aktif');
            $table->timestamps();
        });

        // 3. kasus_master
        Schema::create('kasus_master', function (Blueprint $table) {
            $table->id();
            $table->string('kelompok', 50)->nullable();
            $table->string('jenis_kasus', 100)->nullable();
            $table->integer('masa_aktif_rj')->nullable();
            $table->integer('masa_inaktif_rj')->nullable();
            $table->integer('masa_aktif_ri')->nullable();
            $table->integer('masa_inaktif_ri')->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });

        // Seed Kasus Master
        DB::table('kasus_master')->insert([
            ['kelompok' => 'UMUM', 'jenis_kasus' => 'UMUM', 'masa_aktif_rj' => 5, 'masa_inaktif_rj' => 2, 'masa_aktif_ri' => 5, 'masa_inaktif_ri' => 2],
            ['kelompok' => 'SPESIALIS', 'jenis_kasus' => 'MATA', 'masa_aktif_rj' => 5, 'masa_inaktif_rj' => 2, 'masa_aktif_ri' => 10, 'masa_inaktif_ri' => 2],
            ['kelompok' => 'PSIKIATRI', 'jenis_kasus' => 'JIWA', 'masa_aktif_rj' => 10, 'masa_inaktif_rj' => 5, 'masa_aktif_ri' => 5, 'masa_inaktif_ri' => 5],
            ['kelompok' => 'SPESIALIS', 'jenis_kasus' => 'ORTHOPAEDI', 'masa_aktif_rj' => 10, 'masa_inaktif_rj' => 2, 'masa_aktif_ri' => 10, 'masa_inaktif_ri' => 2],
            ['kelompok' => 'KHUSUS', 'jenis_kasus' => 'KUSTA', 'masa_aktif_rj' => 15, 'masa_inaktif_rj' => 2, 'masa_aktif_ri' => 15, 'masa_inaktif_ri' => 2],
            ['kelompok' => 'KHUSUS', 'jenis_kasus' => 'KETERGANTUNGAN OBAT', 'masa_aktif_rj' => 15, 'masa_inaktif_rj' => 2, 'masa_aktif_ri' => 15, 'masa_inaktif_ri' => 2],
            ['kelompok' => 'SPESIALIS', 'jenis_kasus' => 'JANTUNG', 'masa_aktif_rj' => 10, 'masa_inaktif_rj' => 2, 'masa_aktif_ri' => 10, 'masa_inaktif_ri' => 2]
        ]);

        // 4. kunjungan
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id('id_kunjungan');
            $table->string('no_rm', 20)->nullable();
            $table->string('nama_pasien', 100)->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->text('diagnosa')->nullable();
            $table->timestamps();

            $table->foreign('no_rm')->references('no_rm')->on('pasien')->onDelete('cascade')->onUpdate('cascade');
        });

        // 5. dokumen_rekam_medis
        Schema::create('dokumen_rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm', 20)->nullable();
            $table->string('nama_file', 255)->nullable();
            $table->string('file_original', 255)->nullable();
            $table->string('file_compressed', 255)->nullable();
            $table->string('engine', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();

            $table->foreign('no_rm')->references('no_rm')->on('pasien')->onDelete('set null')->onUpdate('cascade');
        });

        // 6. ocr_result
        Schema::create('ocr_result', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dokumen_id')->nullable();
            $table->longText('ocr_text')->nullable();
            $table->longText('ai_result')->nullable();
            $table->float('confidence')->nullable();
            $table->timestamps();

            $table->foreign('dokumen_id')->references('id')->on('dokumen_rekam_medis')->onDelete('cascade');
        });

        // 7. validasi_data
        Schema::create('validasi_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dokumen_id')->nullable();
            $table->string('no_rm', 20)->nullable();
            $table->string('nama_pasien', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->text('diagnosa')->nullable();
            $table->string('dokter', 100)->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamps();

            $table->foreign('dokumen_id')->references('id')->on('dokumen_rekam_medis');
            $table->foreign('verified_by')->references('id')->on('users');
        });

        // 8. retensi
        Schema::create('retensi', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm', 20)->nullable();
            $table->unsignedBigInteger('jenis_kasus_id')->nullable();
            $table->enum('jenis_layanan', ['RJ', 'RI'])->nullable();
            $table->date('tanggal_kunjungan_terakhir')->nullable();
            $table->integer('masa_aktif')->nullable();
            $table->integer('masa_inaktif')->nullable();
            $table->integer('selisih_tahun')->nullable();
            $table->enum('status_retensi', ['Aktif', 'Inaktif', 'Siap Musnah'])->nullable();
            $table->dateTime('tanggal_proses')->nullable();
            $table->timestamps();

            $table->foreign('no_rm')->references('no_rm')->on('pasien');
            $table->foreign('jenis_kasus_id')->references('id')->on('kasus_master');
        });

        // 9. daftar_pemusnahan
        Schema::create('daftar_pemusnahan', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm', 20)->nullable();
            $table->date('tanggal_retensi')->nullable();
            $table->enum('status', ['menunggu_persetujuan', 'disetujui', 'dimusnahkan'])->nullable();
            $table->unsignedBigInteger('approved_kepala_rm')->nullable();
            $table->dateTime('tanggal_approval_rm')->nullable();
            $table->unsignedBigInteger('approved_direktur')->nullable();
            $table->dateTime('tanggal_approval_direktur')->nullable();
            $table->timestamps();

            $table->foreign('no_rm')->references('no_rm')->on('pasien');
            $table->foreign('approved_kepala_rm')->references('id')->on('users');
            $table->foreign('approved_direktur')->references('id')->on('users');
        });

        // 10. berita_acara_pemusnahan
        Schema::create('berita_acara_pemusnahan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pemusnahan')->nullable();
            $table->string('nomor_berita_acara', 50)->nullable();
            $table->date('tanggal_pemusnahan')->nullable();
            $table->string('file_berita_acara', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_pemusnahan')->references('id')->on('daftar_pemusnahan');
        });

        // 11. activity_logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_user', 100)->nullable();
            $table->string('modul', 50)->nullable();
            $table->string('aksi', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
        
        // Seed users
        DB::table('users')->insert([
            ['username' => 'admin', 'password' => bcrypt('admin123'), 'nama_lengkap' => 'Administrator', 'email' => 'admin@mail.com', 'role' => 'Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['username' => 'staff', 'password' => bcrypt('staff123'), 'nama_lengkap' => 'Staff User', 'email' => 'staff@mail.com', 'role' => 'Staff', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('berita_acara_pemusnahan');
        Schema::dropIfExists('daftar_pemusnahan');
        Schema::dropIfExists('retensi');
        Schema::dropIfExists('validasi_data');
        Schema::dropIfExists('ocr_result');
        Schema::dropIfExists('dokumen_rekam_medis');
        Schema::dropIfExists('kunjungan');
        Schema::dropIfExists('kasus_master');
        Schema::dropIfExists('pasien');
        Schema::dropIfExists('users');
    }
};
