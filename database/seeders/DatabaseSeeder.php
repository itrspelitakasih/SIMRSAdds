<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@rs.local'],
            [
                'name' => 'Admin IT',
                'password' => bcrypt('password'),
            ]
        );

        $admin->assignRole('Admin');
    }
}
