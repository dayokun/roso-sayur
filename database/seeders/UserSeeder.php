<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@rososayur.test'],
            [
                'name' => 'Admin IT',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN_IT,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'roso@rososayur.test'],
            [
                'name' => 'Admin Roso Sayur',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN_ROSO,
                'email_verified_at' => now(),
            ]
        );
    }
}
