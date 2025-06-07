<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'testuser@example.com'],
            [
                'name' => 'Usuario de Prueba',
                'password' => Hash::make('password123'),
                'language' => 'es',
            ]
        );

        $user->assignRole('user');
    }
}
