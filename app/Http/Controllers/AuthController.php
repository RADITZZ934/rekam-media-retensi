<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\ActivityLogService;

class AuthController extends Controller
{
    /**
     * Authenticate the user and start the session
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Self-healing: Upgrade MD5 passwords to Bcrypt dynamically on login
        $user = User::where('username', $request->username)->first();
        if ($user && strlen($user->password) === 32) {
            if (md5($request->password) === $user->password) {
                // Correct password: upgrade to Bcrypt
                $user->update([
                    'password' => \Illuminate\Support\Facades\Hash::make($request->password)
                ]);
            } else {
                // Incorrect password: return error immediately to avoid Bcrypt hasher exception
                return response()->json([
                    'success' => false,
                    'message' => 'Username atau password salah.',
                ], 401);
            }
        }

        // Attempt login with status filter
        if (Auth::attempt(array_merge($credentials, ['status' => 'Aktif']))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            $user->update([
                'last_login' => now(),
            ]);

            ActivityLogService::log('Autentikasi', 'Login', 'User berhasil login ke sistem');

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nama_lengkap' => $user->nama_lengkap,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);
        }

        // Check if user exists but non-active
        $userExists = User::where('username', $request->username)->first();
        if ($userExists && $userExists->status !== 'Aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.',
            ], 403);
        }

        return response()->json([
            'success' => false,
            'message' => 'Username atau password salah.',
        ], 401);
    }

    /**
     * Terminate the session and logout the user
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLogService::log('Autentikasi', 'Logout', 'User berhasil logout dari sistem');
        }
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * Get the authenticated user profile details
     */
    public function me()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nama_lengkap' => $user->nama_lengkap,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated',
        ], 401);
    }
}
