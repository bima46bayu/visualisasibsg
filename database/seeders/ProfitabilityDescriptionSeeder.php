<?php

namespace Database\Seeders;

use App\Models\ProfitabilityDescription;
use Illuminate\Database\Seeder;

class ProfitabilityDescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'pendapatan' => ['Pendapatan Proyek'],
            'hpp' => ['HPP Proyek'],
            'biaya_marketing' => ['Iklan & Promosi'],
            'biaya_admin' => ['Gaji Karyawan'],
            'biaya_non_ops' => ['Biaya Administrasi Bank'],
            'pendapatan_lain' => ['Pendapatan Bunga Bank'],
            'biaya_lain' => ['Bunga Pinjaman'],
            'pajak' => ['Pajak Penghasilan (PPh)'],
        ];

        foreach ($defaults as $category => $names) {
            foreach ($names as $name) {
                ProfitabilityDescription::firstOrCreate([
                    'category' => $category,
                    'name' => $name,
                ]);
            }
        }
    }
}
