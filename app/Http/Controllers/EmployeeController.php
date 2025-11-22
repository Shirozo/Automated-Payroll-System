<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        //
        try {
            $request->validated();

            $user = User::create([
                "name" => $request->name,
                "username" => $request->employee_number,
                "password" => $request->employee_number,
            ]);

            $employee = Employee::create(array_merge($request->validated(), [
                "user_id" => $user->id
            ]));

            return redirect()->route("employee.show")->with('success', 'Employee created successfully');
        } catch (\Throwable $th) {
            return redirect()->route("employee.show")->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //

        $employee = Employee::with(['user', 'position'])->get();
        $nextId = (Employee::max('id') ?? 0) + 1;
        $positions = Position::all();
        return inertia("Employee", [
            "positions" => $positions,
            "nextId" => date("Y") . "-" . str_pad((string)$nextId, 5, "0", STR_PAD_LEFT),
            "initialEmployees" => $employee
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $request->validated();

        DB::beginTransaction();

        $user = User::find($employee->user_id);

        if ($request->password) {
            $user->update([
                "password" => Hash::make($request->password)
            ]);
        }

        $user->update([
            "name" => $request->name
        ]);

        $employee->update($request->validated());
        
        DB::commit();
        

        return redirect()->route("employee.show")->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        //
    }
}
