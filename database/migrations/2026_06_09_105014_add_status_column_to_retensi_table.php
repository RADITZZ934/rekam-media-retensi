<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add status column to retensi
        Schema::table('retensi', function (Blueprint $table) {
            $table->enum('status', ['Aktif', 'Inaktif', 'Siap Dimusnahkan', 'Dimusnahkan'])
                  ->default('Aktif')
                  ->after('selisih_tahun');
        });

        // 2. Migrate old status_retensi data to status
        if (Schema::hasColumn('retensi', 'status_retensi')) {
            DB::table('retensi')->where('status_retensi', 'Aktif')->update(['status' => 'Aktif']);
            DB::table('retensi')->where('status_retensi', 'Inaktif')->update(['status' => 'Inaktif']);
            DB::table('retensi')->where('status_retensi', 'Siap Musnah')->update(['status' => 'Siap Dimusnahkan']);
            
            // 3. Drop old status_retensi column
            Schema::table('retensi', function (Blueprint $table) {
                $table->dropColumn('status_retensi');
            });
        }

        // 4. Update status in daftar_pemusnahan to allow 'menunggu_eksekusi' and 'ditolak'
        // We change the enum to a string for maximum flexibility and to prevent database conversion issues
        Schema::table('daftar_pemusnahan', function (Blueprint $table) {
            $table->string('status', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add back old status_retensi column
        Schema::table('retensi', function (Blueprint $table) {
            $table->enum('status_retensi', ['Aktif', 'Inaktif', 'Siap Musnah'])
                  ->nullable()
                  ->after('selisih_tahun');
        });

        // 2. Migrate data back
        DB::table('retensi')->where('status', 'Aktif')->update(['status_retensi' => 'Aktif']);
        DB::table('retensi')->where('status', 'Inaktif')->update(['status_retensi' => 'Inaktif']);
        DB::table('retensi')->whereIn('status', ['Siap Dimusnahkan', 'Dimusnahkan'])->update(['status_retensi' => 'Siap Musnah']);

        // 3. Drop new status column
        Schema::table('retensi', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // 4. Change status in daftar_pemusnahan back to enum
        Schema::table('daftar_pemusnahan', function (Blueprint $table) {
            $table->enum('status', ['menunggu_persetujuan', 'disetujui', 'dimusnahkan'])->nullable()->change();
        });
    }
};
