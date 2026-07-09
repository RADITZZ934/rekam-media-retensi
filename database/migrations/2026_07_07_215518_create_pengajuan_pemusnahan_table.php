<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create pengajuan_pemusnahan table
        Schema::create('pengajuan_pemusnahan', function (Blueprint $table) {
            $table->id();
            $table->string('no_sk', 100)->unique();
            $table->date('tanggal_pengajuan');
            $table->string('ketua_tim', 100);
            $table->string('anggota_tim_1', 100);
            $table->string('anggota_tim_2', 100)->nullable();
            $table->string('anggota_tim_3', 100)->nullable();
            $table->string('anggota_tim_4', 100)->nullable();
            $table->integer('jumlah_berkas')->default(0);
            $table->enum('status', ['Pending', 'Approved', 'Declined'])->default('Pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Modify role ENUM in users table to include 'Direktur'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Administrator', 'Staff', 'Direktur') NOT NULL;");

        // 3. Add pengajuan_id to daftar_pemusnahan table
        Schema::table('daftar_pemusnahan', function (Blueprint $table) {
            $table->unsignedBigInteger('pengajuan_id')->nullable()->after('id');
            $table->foreign('pengajuan_id')->references('id')->on('pengajuan_pemusnahan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop foreign key and column in daftar_pemusnahan
        Schema::table('daftar_pemusnahan', function (Blueprint $table) {
            $table->dropForeign(['pengajuan_id']);
            $table->dropColumn('pengajuan_id');
        });

        // 2. Drop pengajuan_pemusnahan table
        Schema::dropIfExists('pengajuan_pemusnahan');

        // 3. Revert users table role ENUM
        DB::statement("DELETE FROM users WHERE role = 'Direktur';");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Administrator', 'Staff') NOT NULL;");
    }
};
