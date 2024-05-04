<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            ['name' => 'Bé Thảo', 'user_name' => 'thao12345', 'password' => Hash::make('thao12345'), 'role' => UserRole::ADMIN],
            ['name' => 'Cu Tèo', 'user_name' => 'teo12345', 'password' => Hash::make('teo12345'), 'role' => UserRole::STAFF]
        ];
 
        foreach($employees as $employee) {
            DB::table('users')->insert($employee);
        }
    }
}
