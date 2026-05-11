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
        // 1. Add kasus_id to pasien
        if (!Schema::hasColumn('pasien', 'kasus_id')) {
            Schema::table('pasien', function (Blueprint $table) {
                $table->unsignedBigInteger('kasus_id')->nullable()->after('status_rm');
                $table->foreign('kasus_id')->references('id')->on('kasus_master');
            });
        }

        // 2. Fix Kunjungan: Rename diagnosa to diagnosis for consistency with controller
        if (Schema::hasColumn('kunjungan', 'diagnosa')) {
            Schema::table('kunjungan', function (Blueprint $table) {
                $table->renameColumn('diagnosa', 'diagnosis');
            });
        }

        // 3. Fix OCRResult: Add status, validated_at, and parsed_data logic
        Schema::table('ocr_result', function (Blueprint $table) {
            if (!Schema::hasColumn('ocr_result', 'status')) {
                $table->string('status', 20)->default('draft')->after('confidence');
            }
            if (!Schema::hasColumn('ocr_result', 'validated_at')) {
                $table->timestamp('validated_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('ocr_result', 'parsed_data')) {
                $table->longText('parsed_data')->nullable()->after('ai_result');
            }
            if (!Schema::hasColumn('ocr_result', 'engine')) {
                $table->string('engine', 50)->nullable()->after('parsed_data');
            }
        });

        // 4. Update ValidasiData for consistency
        if (Schema::hasColumn('validasi_data', 'diagnosa')) {
            Schema::table('validasi_data', function (Blueprint $table) {
                $table->renameColumn('diagnosa', 'diagnosis');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->dropForeign(['kasus_id']);
            $table->dropColumn('kasus_id');
        });

        Schema::table('kunjungan', function (Blueprint $table) {
            $table->renameColumn('diagnosis', 'diagnosa');
        });

        Schema::table('ocr_result', function (Blueprint $table) {
            $table->dropColumn(['status', 'validated_at', 'parsed_data', 'engine']);
        });

        Schema::table('validasi_data', function (Blueprint $table) {
            $table->renameColumn('diagnosis', 'diagnosa');
        });
    }
};
