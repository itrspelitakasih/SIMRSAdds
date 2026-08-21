<?php

namespace Database\Seeders;

use App\Helpers\MenuHelper;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_keys(MenuHelper::permissionLabels()) as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }
}
