<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Catat semua log aktivitas
     *
     * @param string $modul
     * @param string $aksi
     * @param string $deskripsi
     * @return void
     */
    public static function log(string $modul, string $aksi, string $deskripsi)
    {
        $user = Auth::user();
        
        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'nama_user' => $user ? $user->nama_lengkap : 'System/Guest',
            'modul' => $modul,
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
