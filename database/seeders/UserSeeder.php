<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Reorderable;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Administrator (Akses Penuh)
        User::updateOrCreate(
            ['email' => 'administrator@gmail.com'], 
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
                'role' => 'administrator',
            ]
        );

        // 2. Akun Manager (Supervisor / Pantau Laporan)
        User::updateOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'Manager Operasional',    
                'password' => Hash::make('password123'),
                'role' => 'manager',
            ]
        );

       // 3. Akun Admin (Operator Input Data)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Balikin ke admin
            [
                'name' => 'Admin Staff',
                'password' => Hash::make('password123'),
                'role' => 'admin', // BALIKIN JADI admin!
            ]
        );
    }
}