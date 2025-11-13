<?php

namespace App\Http\Controllers;

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
        return inertia("EmployeeDashboard", [
            "user_data" => $user_data
        ]);
    }
}
