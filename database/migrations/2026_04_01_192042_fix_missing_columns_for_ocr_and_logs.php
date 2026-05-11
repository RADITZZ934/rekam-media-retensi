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
        // Add columns to ocr_result
        if (Schema::hasTable('ocr_result')) {
            Schema::table('ocr_result', function (Blueprint $table) {
                if (!Schema::hasColumn('ocr_result', 'parsed_data')) {
                    $table->json('parsed_data')->nullable()->after('ai_result');
                }
                if (!Schema::hasColumn('ocr_result', 'updated_at')) {
                    $table->timestamps();
                }
            });
        }

        // Add timestamps to pasien if missing
        if (Schema::hasTable('pasien')) {
            Schema::table('pasien', function (Blueprint $table) {
                if (!Schema::hasColumn('pasien', 'updated_at')) {
                    $table->timestamps();
                }
            });
        }

        // Add kategori to kasus_master
        if (Schema::hasTable('kasus_master')) {
            Schema::table('kasus_master', function (Blueprint $table) {
                if (!Schema::hasColumn('kasus_master', 'kategori')) {
                    $table->string('kategori')->nullable()->after('nama_kasus');
                }
            });
        }

        // Add diagnosa/diagnosis and keterangan to kunjungan
        if (Schema::hasTable('kunjungan')) {
            Schema::table('kunjungan', function (Blueprint $table) {
                if (!Schema::hasColumn('kunjungan', 'diagnosa') && !Schema::hasColumn('kunjungan', 'diagnosis')) {
                    $table->text('diagnosa')->nullable();
                }
                if (!Schema::hasColumn('kunjungan', 'keterangan')) {
                    $table->text('keterangan')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ocr_result', function (Blueprint $table) {
            $table->dropColumn(['parsed_data']);
        });
    }
};
