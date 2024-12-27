<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $userRole = DB::table('roles')->where('name', 'user')->first();

        if (!$userRole) {
            DB::table('roles')->insert([
                'id' => (string) Str::uuid(),
                'name' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Anda juga bisa memastikan role 'admin' ada
        $adminRole = DB::table('roles')->where('name', 'admin')->first();

        if (!$adminRole) {
            DB::table('roles')->insert([
                'id' => (string) Str::uuid(),
                'name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
