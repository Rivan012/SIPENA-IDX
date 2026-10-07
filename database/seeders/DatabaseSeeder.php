<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'admin@idx-bengkulu.id'],
            [
                'name' => 'Admin IDX Bengkulu',
                'password' => Hash::make('password123'),
                'no_telepon' => '081234567890',
                'jabatan' => 'Koordinator Perencanaan',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'no_telepon' => '089876543210',
            ]
        );
    }
}
