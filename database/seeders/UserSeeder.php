<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'username' => 'admin',
            'password' => bcrypt('password123'),
            'nama_lengkap' => 'Administrator RS',
            'email' => 'admin@rsukaliwates.com',
            'role' => 'Administrator',
            'status' => 'Aktif',
            'api_token' => Str::random(80),
            'last_login' => now()->subDays(1),
        ]);

        // Staff users
        User::create([
            'username' => 'petugasrm1',
            'password' => bcrypt('password123'),
            'nama_lengkap' => 'Ahmad Santoso',
            'email' => 'ahmad@rsukaliwates.com',
            'role' => 'Staff',
            'status' => 'Aktif',
            'api_token' => Str::random(80),
            'last_login' => now()->subHours(2),
        ]);

        User::create([
            'username' => 'petugasrm2',
            'password' => bcrypt('password123'),
            'nama_lengkap' => 'Siti Nurhaliza',
            'email' => 'siti@rsukaliwates.com',
            'role' => 'Staff',
            'status' => 'Aktif',
            'api_token' => Str::random(80),
            'last_login' => now()->subHours(5),
        ]);

        User::create([
            'username' => 'petugasrm3',
            'password' => bcrypt('password123'),
            'nama_lengkap' => 'Budi Raharjo',
            'email' => 'budi@rsukaliwates.com',
            'role' => 'Staff',
            'status' => 'Aktif',
            'api_token' => Str::random(80),
            'last_login' => now()->subDays(1),
        ]);

        User::create([
            'username' => 'petugasrm4',
            'password' => bcrypt('password123'),
            'nama_lengkap' => 'Dewi Lestari',
            'email' => 'dewi@rsukaliwates.com',
            'role' => 'Staff',
            'status' => 'Aktif',
            'api_token' => Str::random(80),
            'last_login' => now(),
        ]);

        // Inactive user
        User::create([
            'username' => 'petugasrm5',
            'password' => bcrypt('password123'),
            'nama_lengkap' => 'Rudi Hartono',
            'email' => 'rudi@rsukaliwates.com',
            'role' => 'Staff',
            'status' => 'Nonaktif',
            'api_token' => Str::random(80),
            'last_login' => now()->subMonths(2),
        ]);
    }
}

