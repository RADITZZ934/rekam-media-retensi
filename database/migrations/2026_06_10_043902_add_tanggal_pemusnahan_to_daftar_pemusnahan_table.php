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
        Schema::table('daftar_pemusnahan', function (Blueprint $table) {
            $table->dateTime('tanggal_pemusnahan')->nullable()->after('tanggal_approval_direktur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_pemusnahan', function (Blueprint $table) {
            $table->dropColumn('tanggal_pemusnahan');
        });
    }
};
