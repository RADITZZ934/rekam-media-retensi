<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kasus_master', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kasus')->unique();
            $table->string('nama_kasus');
            $table->text('deskripsi')->nullable();
            $table->string('kategori'); // Kategori penyakit/kasus
            $table->integer('masa_retensi_aktif')->default(5); // tahun
            $table->integer('masa_retensi_inaktif')->default(2); // tahun
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasus_master');
    }
};
