<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //

    public function dashboard(Request $request)
    {
        if (Auth::user()->type == 2) {
            return redirect()->route("index.employee");
        }
        return inertia("Dashboard");
    }

    public function employee(Request $request)
    {
        $user_data = Employee::with(['user', 'position'])
            ->where('user_id', Auth::id())
            ->first();

        $attendance = Attendance::where('employee_id', $user_data->id)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $currentDay = now()->day;

        $formattedAttendance = [];

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth) && $date->day <= $currentDay; $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            $dateString = $date->format('Y-m-d');
            $dayAttendance = $attendance->where('date', $dateString);

            if ($dayAttendance->isEmpty()) {
                $formattedAttendance[] = [
                    'date' => $date->day,
                    'status' => 'absent',
                    'scanned' => []
                ];
            } else {
                $hasLate = $dayAttendance->contains('tag', 'late');
                $status = $hasLate ? 'late' : 'present';

                $scanned = [
                    'am_in' => $dayAttendance->where('action', 'am_login')->first()?->time ?? null,
                    'am_out' => $dayAttendance->where('action', 'am_logout')->first()?->time ?? null,
                    'pm_in' => $dayAttendance->where('action', 'pm_login')->first()?->time ?? null,
                    'pm_out' => $dayAttendance->where('action', 'pm_logout')->first()?->time ?? null,
                ];

                $formattedAttendance[] = [
                    'date' => $date->day,
                    'status' => $status,
                    'scanned' => [$scanned]
                ];
            }
        }

        return inertia("EmployeeDashboard", [
            "user_data" => $user_data,
            "attendance" => $formattedAttendance
        ]);
    }
}
