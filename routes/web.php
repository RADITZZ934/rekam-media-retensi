<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\KasusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RetensiController;
use App\Http\Controllers\AlihMediaController;
use App\Http\Controllers\PemusnahanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ChatAiController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// API Routes
Route::prefix('api')->group(function () {

    // Authentication routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    // 'me' is a GET request to retrieve current user info
    Route::get('me', [AuthController::class, 'me']);

    // ChatAI routes
    Route::post('chatai/send', [ChatAiController::class, 'chat']);
    Route::get('chatai/characters', [ChatAiController::class, 'characters']);

    Route::apiResource('pasien', PasienController::class);
    
    // Kasus routes
    Route::get('kasus/kategori/list', [KasusController::class, 'getKategori']);
    Route::apiResource('kasus', KasusController::class);
    
    // User routes
    Route::get('users/roles/list', [UserController::class, 'getRoles']);
    Route::get('users/statuses/list', [UserController::class, 'getStatuses']);
    Route::apiResource('users', UserController::class);

    // Retensi routes
    Route::get('retensi/summary', [RetensiController::class, 'summary'])->name('retensi.summary');
    Route::get('retensi/kategori/list', [RetensiController::class, 'getKategori']);
    Route::get('retensi/tahun/list', [RetensiController::class, 'getTahun']);
    Route::post('retensi/hitung-ulang', [RetensiController::class, 'hitungUlang']);
    Route::apiResource('retensi', RetensiController::class);

    // Alih Media routes
    Route::get('alih-media', [AlihMediaController::class, 'index']);
    Route::post('alih-media/upload', [AlihMediaController::class, 'upload']);
    Route::post('alih-media/manual', [AlihMediaController::class, 'storeManual']);
    Route::get('alih-media/summary', [AlihMediaController::class, 'summary']);
    Route::get('alih-media/selesai', [AlihMediaController::class, 'getCompleted']);
    Route::get('alih-media/{id}', [AlihMediaController::class, 'show']);
    Route::delete('alih-media/bulk', [AlihMediaController::class, 'bulkDestroy']);
    Route::delete('alih-media/{id}', [AlihMediaController::class, 'destroy']);
    Route::post('alih-media/{id}/start-ocr', [AlihMediaController::class, 'startOcr']);
    Route::post('alih-media/{id}/retry-ocr', [AlihMediaController::class, 'retryOCR']);
    Route::get('alih-media/{id}/ocr-text', [AlihMediaController::class, 'getOcrText']);
    Route::get('alih-media/{id}/file', [AlihMediaController::class, 'getFile']);
    Route::post('alih-media/{id}/save-draft', [AlihMediaController::class, 'saveDraft']);
    Route::post('alih-media/{id}/submit-validasi', [AlihMediaController::class, 'submitValidasi']);
    Route::post('alih-media/parse-ai-text', [AlihMediaController::class, 'parseAiText']);
    Route::post('ai/chat', [AlihMediaController::class, 'chatWithAi']);

    // Pemusnahan routes
    Route::get('pemusnahan', [PemusnahanController::class, 'index']);
    Route::post('pemusnahan/{id}/approve-kepala-rm', [PemusnahanController::class, 'approveKepalaRM']);
    Route::post('pemusnahan/{id}/approve-direktur', [PemusnahanController::class, 'approveDirektur']);
    Route::post('pemusnahan/{id}/musnahkan', [PemusnahanController::class, 'musnahkan']);
    Route::post('pemusnahan/{id}/generate-berita-acara', [PemusnahanController::class, 'generateBeritaAcara']);
    Route::post('pemusnahan/{id}/reject', [PemusnahanController::class, 'reject']);

    // Settings routes
    Route::get('settings', [SettingController::class, 'index']);
    Route::post('settings', [SettingController::class, 'update']);

    // Dashboard routes
    Route::get('dashboard/summary', function () {
        $aktif = \App\Models\Retensi::where('status', 'Aktif')->count();
        $inaktif = \App\Models\Retensi::where('status', 'Inaktif')->count();
        $siapMusnah = \App\Models\Retensi::where('status', 'Siap Dimusnahkan')->count();
        $didigitalisasi = \App\Models\DokumenRekamMedis::whereIn('status', ['completed', 'validated'])->count();

        // Aktivitas Terbaru (5 latest dokumen)
        $recentDocs = \App\Models\DokumenRekamMedis::latest('created_at')->take(5)->get();
        $aktivitas = $recentDocs->map(function ($doc) {
            $action = 'diproses';
            $status_color = 'blue';
            if (in_array($doc->status, ['completed', 'validated'])) {
                $action = 'telah didigitalisasi';
                $status_color = 'green';
            } elseif ($doc->status === 'uploaded' || $doc->status === 'ready') {
                $action = 'diupload';
                $status_color = 'blue';
            } elseif ($doc->status === 'failed') {
                $action = 'gagal diproses';
                $status_color = 'red';
            }

            return [
                'text' => "Dokumen {$doc->nama_file} {$action}",
                'time' => $doc->created_at ? $doc->created_at->diffForHumans() : 'baru saja',
                'color' => $status_color
            ];
        });

        // Statistik Bulanan (this month)
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $totalDiproses = \App\Models\DokumenRekamMedis::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)->count();

        $totalDidigitalisasi = \App\Models\DokumenRekamMedis::whereIn('status', ['completed', 'validated'])
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)->count();

        $totalDimusnahkan = \App\Models\Pemusnahan::where('status', 'Dimusnahkan')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)->count();

        return response()->json([
            'success' => true,
            'summary' => [
                'aktif' => $aktif,
                'inaktif' => $inaktif,
                'siapMusnah' => $siapMusnah,
                'didigitalisasi' => $didigitalisasi,
            ],
            'aktivitas' => $aktivitas,
            'statistik' => [
                'diproses' => $totalDiproses,
                'didigitalisasi' => $totalDidigitalisasi,
                'dimusnahkan' => $totalDimusnahkan,
            ]
        ]);
    });
});

// Test Koneksi DB
Route::get('/test-db', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        return "Database connected successfully!";
    } catch (\Exception $e) {
        return "Connection failed: " . $e->getMessage();
    }
});

// Catch-all untuk Vue Router
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|test-db).*$')->name('spa');