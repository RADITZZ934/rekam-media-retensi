<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Pasien;
use App\Models\AppSetting;
use App\Services\RetensiService;
use App\Http\Controllers\PemusnahanController;
use App\Services\ActivityLogService;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/**
 * Dinamic Scheduler for Retention Status Update
 * Controlled by AppSetting 'retention_update_interval'
 */
Schedule::call(function () {
    // 1. Check if it's time to run based on dynamic setting
    $interval = (int) AppSetting::get('retention_update_interval', 24);
    $unit = AppSetting::get('retention_update_unit', 'hours');
    $lastUpdate = AppSetting::get('last_retention_update');

    $shouldRun = false;
    if (!$lastUpdate) {
        $shouldRun = true;
    } else {
        if ($unit === 'minutes') {
            $nextRunTime = Carbon::parse($lastUpdate)->addMinutes($interval);
        } else {
            $nextRunTime = Carbon::parse($lastUpdate)->addHours($interval);
        }

        if (Carbon::now()->greaterThanOrEqualTo($nextRunTime)) {
            $shouldRun = true;
        }
    }

    if ($shouldRun) {
        // Run logic
        $retensiService = app(RetensiService::class);
        $pasienList = Pasien::with(['kasus', 'kunjunganTerakhir', 'retensi'])->get();

        foreach ($pasienList as $pasien) {
            $retensiService->calculateForPasien($pasien);
        }

        // 2. Import Dokumen dengan status "Siap Dimusnahkan" otomatis ke tabel pemusnahan
        app(PemusnahanController::class)->importSiapMusnah();

        // 3. Update Last Run Time
        AppSetting::set('last_retention_update', Carbon::now()->toDateTimeString());

        // 4. Catat log
        ActivityLogService::log(
            'System Scheduler',
            'Otomasi Harian',
            "Telah menghitung ulang retensi ({$pasienList->count()} pasien) berdasarkan interval {$interval} " . ($unit === 'minutes' ? 'menit' : 'jam') . "."
        );
    }
})->everyMinute(); // Check every minute if it's time to run the task
