<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
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
            ->get();
                
        $absent = $attendance->where("tag", "absent")->count();
        $present = $attendance->where("tag", "present")->count();

        return inertia("EmployeeDashboard", [
            "user_data" => $user_data,
            "attendance" => $attendance,
            "absent" => $absent,
            "present" => $present
        ]);
    }
}
