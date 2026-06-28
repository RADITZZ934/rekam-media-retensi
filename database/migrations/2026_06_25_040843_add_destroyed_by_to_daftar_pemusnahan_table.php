<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daftar_pemusnahan', function (Blueprint $table) {
            $table->unsignedBigInteger('destroyed_by')->nullable()->after('tanggal_pemusnahan');
            $table->foreign('destroyed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_pemusnahan', function (Blueprint $table) {
            $table->dropForeign(['destroyed_by']);
            $table->dropColumn('destroyed_by');
        });
    }
};
