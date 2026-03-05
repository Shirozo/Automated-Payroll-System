<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayrollRequest;
use App\Models\Attendance;
use App\Models\Configuration;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollData;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function view(Request $request, Payroll $payroll, PayrollService $payrollService)
    {
        //
        return $payrollService->generatePayrollPdf($payroll);
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

        DB::beginTransaction();

        $date = Carbon::parse($request->month . " " . $request->year);
        $daysInMonth = $date->daysInMonth;
        $workingDays = 0;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            if ($date->copy()->day($i)->isWeekday()) {
                $workingDays++;
            }
        }

        $employees = Employee::all();

        $payroll = Payroll::create([
            "name" => $request->month . " " . $request->year . " Payroll (" . $name_helper[$request->deduction] . ")",
            "deduction" => $request->deduction,
            "month" => $request->month,
            "year" => $request->year,
            "employee_count" => $employees->count()
        ]);


        foreach ($employees as $employee) {
            $attendances = Attendance::where("employee_id", $employee->id)
                ->whereMonth("date", $date->month)
                ->whereYear("date", $date->year)
                ->get();

            $presentDays = $attendances->where("tag", "present")->count();
            $absentDays = $attendances->where("tag", "absent")->count();

            $pera = (float)Configuration::where("name", "pera")->first()->value;
            $local_pave = (float)Configuration::where("name", "local_pave")->first()->value;
            $pagibig_premium = (float)Configuration::where("name", "pag_ibig_premium")->first()->value;
            $essu_ffa = (float)Configuration::where("name", "essu_ffa")->first()->value;
            $essu_union = (float)Configuration::where("name", "essu_union")->first()->value;

            $salary = (float)$employee->position->salary;

            if ($request->deduction == "retiree") {
                $custom_deduction = $salary / 30 / 2;
            } elseif ($request->deduction == "death_aid") {
                $custom_deduction = $salary / 30;
            } else {
                $custom_deduction = $salary / 30 / 4;
            }

            PayrollData::create([
                "payroll_id" => $payroll->id,
                "employee_id" => $employee->id,
                "days_present" => $presentDays,
                "days_absent" => $absentDays,
                "rate" => $salary,
                "pera" => $pera,
                "period_earned" => $salary + $pera,
                "gsis_mpl" => $employee->deduction_gsis_mpl ? (float)$employee->deduction_gsis_mpl : 0,
                "philhealth" => $salary * 0.05 / 2,
                "local_pave" => $local_pave,
                "life_retirement" => $salary * 0.09,
                "pagibig_premium" => $pagibig_premium,
                "pagibig_mp3" => $employee->deduction_pagibig_mp3 ? (float)$employee->deduction_pagibig_mp3 : 0,
                "pagibig_calamity" => $employee->deduction_pagibig_calamity ? (float)$employee->deduction_pagibig_calamity : 0,
                "city_savings" => $employee->deduction_city_savings ? (float)$employee->deduction_city_savings : 0,
                "withholding_tax" => $employee->deduction_withholding_tax ? (float)$employee->deduction_withholding_tax : 0,
                "absence_wo_pay" => $salary / 22 * $absentDays,
                "cottage_rental" => $employee->deduction_igp_cottage ? (float)$employee->deduction_igp_cottage : 0,
                "essu_ffa" => $essu_ffa,
                "custom_deduction" => $custom_deduction,
                "essu_union" => $essu_union,
                "cfi" => $employee->deduction_cfi ? (float)$employee->deduction_cfi : 0,
            ]);
        }

        DB::commit();

        return redirect()->route("payroll.show")->with("success", "Payroll Created!");
    }

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

        return redirect()->route("payroll.show")->with([
            "success" => "Payroll Deleted!",
        ]);
    }

    public function updateVisible(Request $request, Payroll $payroll)
    {
        $payroll->update([
            "viewable" => !$payroll->viewable
        ]);

        return redirect()->route("payroll.show")->with([
            "success" => "Payroll Visibility Updated!",
        ]);
    }
}
