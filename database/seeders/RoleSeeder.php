<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'hr'];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}
