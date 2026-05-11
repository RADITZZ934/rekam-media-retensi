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
        Schema::table('dokumen_rekam_medis', function (Blueprint $table) {
            $table->string('status', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_rekam_medis', function (Blueprint $table) {
            $table->enum('status', ['uploaded', 'processing', 'completed', 'failed', 'ready'])->nullable()->change();
        });
    }
};
