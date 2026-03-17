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
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm');
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->text('diagnosis')->nullable();
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
        Schema::dropIfExists('kunjungan');
    }
};
