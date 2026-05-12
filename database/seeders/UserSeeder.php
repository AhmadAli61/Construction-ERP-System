<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::pluck('id', 'name');

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'role_id' => $roles['admin'],
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'hr@example.com'],
            [
                'name' => 'HR User',
                'role_id' => $roles['hr'],
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]
        );

        $this->command->info('Users seeded successfully!');
    }
}
