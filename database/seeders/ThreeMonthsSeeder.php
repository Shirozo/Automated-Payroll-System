<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Device;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollData;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ThreeMonthsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Positions
        $positions = [
            ['name' => 'Manager', 'salary' => 50000],
            ['name' => 'Senior Developer', 'salary' => 40000],
            ['name' => 'Junior Developer', 'salary' => 25000],
            ['name' => 'HR Staff', 'salary' => 20000],
        ];

        foreach ($positions as $pos) {
            Position::firstOrCreate(['name' => $pos['name']], ['salary' => $pos['salary']]);
        }

        $allPositions = Position::all();

        // 2. Create Devices
        $devices = [];
        for ($i = 1; $i <= 5; $i++) {
            $devices[] = Device::create([
                'name' => 'Biometric Device ' . $i,
                'mac' => Str::upper(Str::random(12)),
                'ip' => '192.168.1.' . (100 + $i),
                'status' => 'online',
                'last_seen' => now(),
            ]);
        }
        $deviceIds = collect($devices)->pluck('id')->toArray();

        // 3. Create Users/Employees
        $usersData = [
            ['name' => 'Admin User', 'username' => 'admin', 'password' => 'password', 'type' => '1'],
            ['name' => 'John Doe', 'username' => 'johndoe', 'password' => 'password', 'type' => '2'],
            ['name' => 'Jane Smith', 'username' => 'janesmith', 'password' => 'password', 'type' => '2'],
            ['name' => 'Alice Johnson', 'username' => 'alicej', 'password' => 'password', 'type' => '2'],
        ];

        // Add 10 more employees
        for ($i = 1; $i <= 10; $i++) {
            $usersData[] = [
                'name' => 'Employee ' . $i,
                'username' => 'employee' . $i,
                'password' => 'password',
                'type' => '2',
            ];
        }

        $employees = [];

        foreach ($usersData as $userData) {
            $user = User::firstOrCreate(
                ['username' => $userData['username']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'type' => $userData['type']
                ]
            );

            // Create Employee record if not exists
            $employee = Employee::where('user_id', $user->id)->first();
            if (!$employee) {
                $employee = Employee::create([
                    'user_id' => $user->id,
                    'position_id' => $allPositions->random()->id,
                    'employee_number' => 'EMP-' . Str::upper(Str::random(6)),
                    'deduction_gsis_mpl' => rand(100, 500),
                    'deduction_pagibig_mp3' => rand(100, 500),
                    'deduction_pagibig_calamity' => rand(50, 200),
                    'deduction_city_savings' => rand(100, 300),
                    'deduction_withholding_tax' => rand(500, 1500),
                    'deduction_igp_cottage' => rand(50, 200),
                    'deduction_cfi' => rand(50, 200),
                    'device' => 'Device-' . Str::random(3),
                    'fingerprint_id' => rand(1000, 9999),
                ]);
            }
            $employees[] = $employee;
        }

        // 4. Generate Attendance for last 3 months
        // From Dec 1, 2025 to Feb 28, 2026
        $startDate = Carbon::create(2025, 12, 1);
        $endDate = Carbon::create(2026, 2, 28);

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            foreach ($employees as $employee) {
                // Randomly skip some days (absent)
                if (rand(1, 100) <= 5) {
                    continue; 
                }

                // AM Use random times
                // AM Login: 7:30 - 8:30
                $amLogin = $currentDate->copy()->setTime(7, 30)->addMinutes(rand(0, 60));
                // Tag: Late if after 8:00 (example logic, adjust as needed)
                $tag = $amLogin->format('H:i') > '08:00' ? 'late' : 'present';

                $amLogout = $currentDate->copy()->setTime(12, 0)->addMinutes(rand(0, 30));
                $pmLogin = $currentDate->copy()->setTime(13, 0)->addMinutes(rand(0, 30));
                $pmLogout = $currentDate->copy()->setTime(17, 0)->addMinutes(rand(0, 60));

                Attendance::create([
                    'employee_id' => $employee->id,
                    'device_id' => $deviceIds[array_rand($deviceIds)],
                    'tag' => $tag,
                    'date' => $currentDate->format('Y-m-d'),
                    'am_login' => $amLogin->format('H:i:s'),
                    'am_logout' => $amLogout->format('H:i:s'),
                    'pm_login' => $pmLogin->format('H:i:s'),
                    'pm_logout' => $pmLogout->format('H:i:s'),
                ]);
            }

            $currentDate->addDay();
        }
    }
}
