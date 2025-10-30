<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Buat user Manager (Admin lama kamu)
        $manager = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Manager Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => $now,
            ]
        );

        // Buat user Staff
        $staff = User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff Demo',
                'password' => Hash::make('secret123'),
                'email_verified_at' => $now,
            ]
        );

        // Pastikan roles sudah ada dulu (buat via RolesAndPermissionsSeeder)
        $managerRole = DB::table('roles')->where('name', 'manager')->first();
        $staffRole   = DB::table('roles')->where('name', 'staff')->first();

        if ($managerRole && $staffRole) {
            DB::table('user_roles')->upsert([
                ['user_id' => $manager->id, 'role_id' => $managerRole->id, 'created_at'=>$now, 'updated_at'=>$now],
                ['user_id' => $staff->id,   'role_id' => $staffRole->id,   'created_at'=>$now, 'updated_at'=>$now],
            ], ['user_id','role_id'], ['updated_at']);
        }
    }
}
