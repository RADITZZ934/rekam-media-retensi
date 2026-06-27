<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Get daftar user dengan filter dan search
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter role
        if ($request->has('role') && $request->role) {
            $query->byRole($request->role);
        }

        // Filter status
        if ($request->has('status') && $request->status) {
            $query->byStatus($request->status);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Transform data untuk frontend
        $users->getCollection()->transform(function ($item) {
            return $this->formatUserData($item);
        });

        return response()->json($users);
    }

    /**
     * Get detail user
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $this->formatUserData($user),
        ]);
    }

    /**
     * Store user baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username|min:4|max:50',
            'password' => 'required|string|min:6',
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:100',
            'role' => 'required|in:Administrator,Staff',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        // Encrypt password
        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $this->formatUserData($user),
        ]);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|unique:users,username,' . $id . '|min:4|max:50',
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id . '|max:100',
            'role' => 'required|in:Administrator,Staff',
            'status' => 'required|in:Aktif,Nonaktif',
            'password' => 'nullable|string|min:6',
        ]);

        // Jika password diupdate
        if ($request->has('password') && $request->password) {
            $validated['password'] = $request->password;
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui',
            'data' => $this->formatUserData($user),
        ]);
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Don't allow deleting users with Administrator role
        if ($user->role === 'Administrator') {
            return response()->json([
                'success' => false,
                'message' => 'User dengan role Administrator tidak boleh dihapus',
            ], 403);
        }

        // Don't allow deleting self
        if (auth()->check() && auth()->user()->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun sendiri',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus',
        ]);
    }

    /**
     * Get list roles
     */
    public function getRoles()
    {
        return response()->json([
            'roles' => ['Administrator', 'Staff'],
        ]);
    }

    /**
     * Get list status
     */
    public function getStatuses()
    {
        return response()->json([
            'statuses' => ['Aktif', 'Nonaktif'],
        ]);
    }

    /**
     * Get user activity logs for "Log Aktivitas" screen
     */
    public function activityLogs(Request $request)
    {
        $users = User::get();

        $latestLogins = \App\Models\ActivityLog::where('aksi', 'Login')
            ->whereIn('user_id', $users->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('user_id')
            ->keyBy('user_id');

        $latestLogouts = \App\Models\ActivityLog::where('aksi', 'Logout')
            ->whereIn('user_id', $users->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('user_id')
            ->keyBy('user_id');

        $latestAuthLogs = \App\Models\ActivityLog::whereIn('aksi', ['Login', 'Logout'])
            ->whereIn('user_id', $users->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('user_id')
            ->keyBy('user_id');

        $logs = $users->map(function ($user) use ($latestLogins, $latestLogouts, $latestAuthLogs) {
            $latestLogin = $latestLogins->get($user->id);
            $latestLogout = $latestLogouts->get($user->id);
            $latestAuthLog = $latestAuthLogs->get($user->id);

            $status = 'Sudah Logout';
            if ($latestAuthLog && $latestAuthLog->aksi === 'Login') {
                $status = 'Sedang Login';
            }

            return [
                'namaUser' => $user->nama_lengkap ?? $user->username,
                'role' => $user->role,
                'loginTerakhir' => $latestLogin ? $latestLogin->created_at->format('Y-m-d H:i') : '-',
                'logoutTerakhir' => $latestLogout ? $latestLogout->created_at->format('Y-m-d H:i') : '-',
                'status' => $status,
            ];
        });

        return response()->json($logs);
    }

    /**
     * Format data user untuk response
     */
    private function formatUserData($user)
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'nama_lengkap' => $user->nama_lengkap,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'last_login' => $user->last_login?->format('d/m/Y H:i'),
            'created_at' => $user->created_at,
        ];
    }
}
