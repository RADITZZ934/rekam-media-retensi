<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\ActivityLogService;

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
        $users = $query->orderBy('id', 'asc')->paginate($perPage);

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
            'role' => 'required|in:Administrator,Staff,Direktur',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        // Encrypt password
        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        ActivityLogService::log('User', 'Tambah User', "User menambahkan pengguna baru: {$user->username} ({$user->role})");

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
            'role' => 'required|in:Administrator,Staff,Direktur',
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

        ActivityLogService::log('User', 'Update User', "User memperbarui pengguna: {$user->username}");

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

        ActivityLogService::log('User', 'Hapus User', "User menghapus pengguna: {$user->username}");

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
            'roles' => ['Administrator', 'Staff', 'Direktur'],
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

    public function activityLogs(Request $request)
    {
        $logs = \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'waktu' => $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-',
                'namaUser' => $item->nama_user ?: ($item->user?->nama_lengkap ?? $item->user?->username ?? 'System'),
                'modul' => $item->modul,
                'aksi' => $item->aksi,
                'deskripsi' => $item->deskripsi,
                'ipAddress' => $item->ip_address ?? '-',
                'userAgent' => $item->user_agent ?? '-',
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
