<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Comment out user creation since users table migration doesn't exist
        // User::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Seed user
        $this->call(UserSeeder::class);

        // Seed pasien dan data relasi
        $this->call(PasienSeeder::class);
        
        // Seed kasus master
        $this->call(KasusSeeder::class);
    }
}
