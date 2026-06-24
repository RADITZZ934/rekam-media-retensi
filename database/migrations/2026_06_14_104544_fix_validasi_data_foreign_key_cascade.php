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
        Schema::table('validasi_data', function (Blueprint $table) {
            try {
                $table->dropForeign('validasi_data_ibfk_1');
            } catch (\Exception $e) {
                try {
                    $table->dropForeign('validasi_data_dokumen_id_foreign');
                } catch (\Exception $ex) {
                    // ignore
                }
            }
        });

        Schema::table('validasi_data', function (Blueprint $table) {
            $table->foreign('dokumen_id')
                  ->references('id')
                  ->on('dokumen_rekam_medis')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('validasi_data', function (Blueprint $table) {
            try {
                $table->dropForeign(['dokumen_id']);
            } catch (\Exception $e) {
                // ignore
            }
        });

        Schema::table('validasi_data', function (Blueprint $table) {
            $table->foreign('dokumen_id')
                  ->references('id')
                  ->on('dokumen_rekam_medis');
        });
    }
};
