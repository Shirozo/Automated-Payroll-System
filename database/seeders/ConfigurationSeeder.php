<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $configurations = [
            ['name' => 'morning_login', 'value' => '07:00:00'],
            ['name' => 'morning_logout', 'value' => '12:00:00'],
            ['name' => 'afternoon_login', 'value' => '13:00:00'],
            ['name' => 'afternoon_logout', 'value' => '17:00:00'],
            ['name' => 'grace_period', 'value' => '15'],
            ['name' => 'pera', 'value' => '2000'],
            ['name' => 'philhealth', 'value' => 'BS * 5% / 2'],
            ['name' => 'local_pave', 'value' => '40'],
            ['name' => 'life_retirement', 'value' => 'BS * 9%'],
            ['name' => 'pag_ibig_premium', 'value' => '200'],
            ['name' => 'essu_ffa', 'value' => '20'],
            ['name' => 'retiree_fin_asst', 'value' => 'BS / 30'],
            ['name' => 'essu_union', 'value' => '30'],
        ];

         foreach ($configurations as $config) {
            Configuration::create($config);
        }
    }
}
