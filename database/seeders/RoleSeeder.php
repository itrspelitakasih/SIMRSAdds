<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $petugas = Role::firstOrCreate(['name' => 'Petugas', 'guard_name' => 'web']);

        $admin->syncPermissions(Permission::all());
        $petugas->syncPermissions(['tickets', 'laporan', 'documents']);
    }
}
