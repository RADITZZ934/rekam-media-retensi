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
        Schema::table('retensi', function (Blueprint $table) {
            if (!Schema::hasColumn('retensi', 'tanggal_batas_aktif')) {
                $table->date('tanggal_batas_aktif')->nullable()->after('masa_inaktif');
            }
            if (!Schema::hasColumn('retensi', 'tanggal_batas_musnah')) {
                $table->date('tanggal_batas_musnah')->nullable()->after('tanggal_batas_aktif');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retensi', function (Blueprint $table) {
            if (Schema::hasColumn('retensi', 'tanggal_batas_aktif')) {
                $table->dropColumn('tanggal_batas_aktif');
            }
            if (Schema::hasColumn('retensi', 'tanggal_batas_musnah')) {
                $table->dropColumn('tanggal_batas_musnah');
            }
        });
    }
};
