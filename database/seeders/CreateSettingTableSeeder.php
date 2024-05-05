<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting_tables = [
            ['type' => 'VIP', 'price' => 200000],
            ['type' => 'NORMAL', 'price' => 100000],

        ];
 
        foreach($setting_tables as $setting_table) {
            DB::table('setting_table')->insert($setting_table);
        }
    }
}
