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
            $table->unsignedBigInteger('kasus_id')->nullable()->after('dokter');
            $table->foreign('kasus_id')->references('id')->on('kasus_master')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('validasi_data', function (Blueprint $table) {
            $table->dropForeign(['kasus_id']);
            $table->dropColumn('kasus_id');
        });
    }
};
