<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Ambil UUID role admin
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');

        // Cek apakah role 'admin' ada
        if (!$adminRoleId) {
            // Jika role 'admin' belum ada, tambahkan role tersebut terlebih dahulu
            $adminRoleId = (string) Str::uuid();
            DB::table('roles')->insert([
                'id' => $adminRoleId,
                'name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Buat atau update user dengan email 'test@example.com'
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role_id' => $adminRoleId,
                'remember_token' => Str::random(10),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
