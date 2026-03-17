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
        Schema::create('retensi', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm')->unique();
            $table->enum('status_retensi', ['Aktif', 'Inaktif', 'Siap Musnah'])->default('Aktif');
            $table->date('tanggal_mulai_retensi')->nullable();
            $table->date('tanggal_akhir_retensi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key ke pasien
            $table->foreign('no_rm')->references('no_rm')->on('pasien')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retensi');
    }
};
