<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayrollRequest;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function view(Request $request, PayrollService $payrollService)
    {
        //
        return $payrollService->generatePayrollPdf([]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePayrollRequest $request)
    {
        //
        $request->validated();

        $name_helper = [
            "retiree" => "Retiree Financial Assistant",
            "death_aid" => "Death Aid",
            "healthcare" => "Healthcare",
        ];

        Payroll::create([
            "name" => $request->month . " " . $request->year . " Payroll (" . $name_helper[$request->deduction] . ")",
            "deduction" => $request->deduction,
            "month" => $request->month,
            "year" => $request->year,
        ]);

        return redirect()->route("payroll.show")->with("success", "Payroll Created!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //

        $availableDates = Attendance::select('date')
            ->get()
            ->map(function ($attendance) {
                $date = Carbon::parse($attendance->date);
                return [
                    'month' => $date->format('F'),
                    'year' => $date->year,
                    'month_num' => $date->month,
                ];
            })
            ->unique(function ($item) {
                return $item['year'] . '-' . $item['month_num'];
            })
            ->sortByDesc(function ($item) {
                return $item['year'] * 100 + $item['month_num'];
            })
            ->values()
            ->map(function ($item) {
                return [
                    'month' => $item['month'],
                    'year' => $item['year'],
                ];
            });
        
        $payroll = Payroll::all();

        return inertia("Payroll", [
            "availableDates" => $availableDates,
            "payroll" => $payroll
        ]);
    }

    public function destroy(Request $request, Payroll $payroll)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $payroll->delete();

        return redirect()->route("position.show")->with([
            "success" => "Payroll Deleted!",
        ]);
    }
}
