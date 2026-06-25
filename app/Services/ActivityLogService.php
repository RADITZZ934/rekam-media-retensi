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
        $userId = null;
        $namaUser = 'System/Guest';

        if ($user) {
            $userId = $user->id;
            $namaUser = $user->nama_lengkap ?? $user->username;
        } else {
            // Check if request has username query param (useful for direct downloads from browser)
            $req = request();
            if ($req && $req->has('username')) {
                $username = $req->input('username');
                $dbUser = \App\Models\User::where('username', $username)->first();
                if ($dbUser) {
                    $userId = $dbUser->id;
                    $namaUser = $dbUser->nama_lengkap ?? $dbUser->username;
                } else {
                    $namaUser = $username;
                }
            }
        }
        
        ActivityLog::create([
            'user_id' => $userId,
            'nama_user' => $namaUser,
            'modul' => $modul,
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
