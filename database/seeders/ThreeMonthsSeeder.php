<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceLog;
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
        $macAddresses = [
            '00:1A:2B:3C:4D:5E',
            'A1:B4:C5:C1:67:AB',
            '08:00:07:A9:B2:EB',
            '74:78:27:11:22:33',
            '74:78:27:11:22:32',
        ];

        $deviceIds = [];
        $deviceMacs = [];
        foreach ($macAddresses as $index => $mac) {
            $device = Device::firstOrCreate(
                ['mac' => $mac],
                [
                    'name' => 'Biometric Device ' . ($index + 1),
                    'ip' => '192.168.1.' . (100 + $index + 1),
                    'status' => 'online',
                    'last_seen' => now(),
                ]
            );
            $deviceIds[] = $device->id;
            $deviceMacs[] = $device->mac;
        }

        // 3. Create Users/Employees
        $usersData = [
            ['name' => 'Admin User', 'email' => 'admin@noemail.com', 'password' => 'password', 'type' => '1'],
            ['name' => 'John Doe', 'email' => 'johndoe@noemail.com', 'password' => 'password', 'type' => '2'],
            ['name' => 'Jane Smith', 'email' => 'janesmith@noemail.com', 'password' => 'password', 'type' => '2'],
            ['name' => 'Alice Johnson', 'email' => 'alicej@noemail.com', 'password' => 'password', 'type' => '2'],
        ];

        // Add 10 more employees
        for ($i = 1; $i <= 10; $i++) {
            $usersData[] = [
                'name' => 'Employee ' . $i,
                'email' => 'employee' . $i . "@noemail.com",
                'password' => 'password',
                'type' => '2',
            ];
        }

        // Pre-assign device to each user to be consistent
        $userDeviceMap = [];
        foreach ($usersData as $index => $userData) {
            $randomIndex = array_rand($deviceIds);
            $userDeviceMap[$userData['email']] = [
                'id' => $deviceIds[$randomIndex],
                'mac' => $deviceMacs[$randomIndex]
            ];
        }

        $employees = [];

        foreach ($usersData as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'type' => $userData['type']
                ]
            );

            // Create Employee record if not exists
            $employee = Employee::where('user_id', $user->id)->first();
            if (!$employee) {
                $assignedDevice = $userDeviceMap[$userData['email']];
                
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
                    'device' => $assignedDevice['mac'], // Use the MAC address of the assigned device
                    'fingerprint_id' => rand(1000, 9999),
                ]);
            }
            $employees[] = $employee;
        }

        // Create mapping for attendance generation
        $employeeDeviceMap = [];
        foreach ($employees as $emp) {
            // Find the device ID based on the employee's assigned device MAC
            $device = Device::where('mac', $emp->device)->first();
            if ($device) {
                $employeeDeviceMap[$emp->id] = $device->id;
            } else {
                // Fallback (shouldn't happen with correct logic above)
                $employeeDeviceMap[$emp->id] = $deviceIds[0];
            }
        }

        // 4. Generate Attendance for last 3 months
        // From Dec 1, 2025 to Feb 28, 2026
        $startDate = Carbon::create(2025, 12, 1);
        $endDate = Carbon::create(2026, 2, 28);

        $currentDate = $startDate->copy();

        $actions = ["am_login", "am_logout", "pm_login", "pm_logout"];

        while ($currentDate->lte($endDate)) {
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            foreach ($employees as $employee) {
                $deviceId = $employeeDeviceMap[$employee->id];

                // Randomly skip some days (absent)
                if (rand(1, 100) <= 5) {
                    Attendance::create([
                        'employee_id' => $employee->id,
                        'device_id' => $deviceId,
                        'tag' => "absent",
                        'date' => $currentDate->format('Y-m-d'),
                    ]);
                    continue;
                }

                // AM Use random times
                // AM Login: 7:30 - 8:30
                $amLogin = $currentDate->copy()->setTime(7, 30)->addMinutes(rand(0, 60));
                $amLogout = $currentDate->copy()->setTime(12, 0)->addMinutes(rand(0, 30));
                $pmLogin = $currentDate->copy()->setTime(13, 0)->addMinutes(rand(0, 30));
                $pmLogout = $currentDate->copy()->setTime(17, 0)->addMinutes(rand(0, 60));

                Attendance::create([
                    'employee_id' => $employee->id,
                    'device_id' => $deviceId,
                    'tag' => "present",
                    'date' => $currentDate->format('Y-m-d'),
                    'am_login' => $amLogin->format('H:i:s'),
                    'am_logout' => $amLogout->format('H:i:s'),
                    'pm_login' => $pmLogin->format('H:i:s'),
                    'pm_logout' => $pmLogout->format('H:i:s'),
                ]);

                $logs = [];
                $timestamp = now();

                foreach ([
                    'am_login' => $amLogin,
                    'am_logout' => $amLogout,
                    'pm_login' => $pmLogin,
                    'pm_logout' => $pmLogout,
                ] as $action => $time) {
                    $logs[] = [
                        'employee_id' => $employee->id,
                        'device_id' => $deviceId,
                        'date' => $currentDate->format('Y-m-d'),
                        'action' => $action,
                        'time' => $time->format('H:i:s'),
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }

                AttendanceLog::insert($logs);
            }

            $currentDate->addDay();
        }
    }
}
