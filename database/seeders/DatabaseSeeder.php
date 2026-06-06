<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'admin',
            'username' => 'admin',
            'password' => '12345678',
            'status' => Status::ACTIVE,
            'role' => Role::ADMIN,
        ]);
    }
}
