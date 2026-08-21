<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            'IGD',
            'Rawat Inap',
            'Rawat Jalan',
            'Farmasi',
            'Laboratorium',
            'Radiologi',
            'Administrasi/Kasir',
            'Rekam Medis',
            'IT',
        ];

        foreach ($units as $name) {
            Unit::firstOrCreate(['name' => $name]);
        }
    }
}
