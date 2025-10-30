<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

// tambahkan seeder lain yang kamu punya
use Database\Seeders\CategorySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ pastikan user test default masih ada
        if (!DB::table('users')->where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name'  => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // ✅ jalankan semua seeder tambahan
        $this->call([
            CategorySeeder::class,           // seeder kategori kamu
            RolesAndPermissionsSeeder::class, // buat roles, permissions, mapping
            UserSeeder::class,                // buat akun manager & staff
        ]);
    }
}
