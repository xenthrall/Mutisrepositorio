<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('Pas#word2341');

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => $password,
            ]
        );

        // Asignar rol SUPER ADMIN
        $admin->syncRoles(['SUPER ADMIN']);

        User::updateOrCreate(
            ['email' => 'rector@gmail.com'],
            [
                'name' => 'Rector',
                'password' => $password,
            ]
        );

        User::updateOrCreate(
            ['email' => 'secretaria@gmail.com'],
            [
                'name' => 'Secretaria',
                'password' => $password,
            ]
        );
    }
}