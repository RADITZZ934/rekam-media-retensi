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
        Schema::table('pengajuan_pemusnahan', function (Blueprint $table) {
            $table->string('anggota_tim_5', 100)->nullable()->after('anggota_tim_4');
            $table->string('anggota_tim_6', 100)->nullable()->after('anggota_tim_5');
            $table->string('anggota_tim_7', 100)->nullable()->after('anggota_tim_6');
            $table->string('anggota_tim_8', 100)->nullable()->after('anggota_tim_7');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_pemusnahan', function (Blueprint $table) {
            $table->dropColumn(['anggota_tim_5', 'anggota_tim_6', 'anggota_tim_7', 'anggota_tim_8']);
        });
    }
};
