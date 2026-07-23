<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\Status;
use App\Enums\Type;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Store;
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

        Brand::create([
            'name' => 'Apple',
            'made' => 'EEUU',
            'color_fg' => '#000000',
            'color_bg' => '#ffffff',
        ]);

        Category::create([
            'name' => 'Celulares',
        ]);

        Store::create([
            'name' => 'Tienda 1',
            'type' => Type::STORE,
            'status' => Status::ACTIVE,
            'address' => 'AV-1'
        ]);
        Store::create([
            'name' => 'Tienda 2',
            'type' => Type::STORE,
            'status' => Status::ACTIVE,
            'address' => 'AV-1'
        ]);
    }
}
