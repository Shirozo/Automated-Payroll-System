<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Configuration;
use App\Models\Device;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{

    public function store(StoreAttendanceRequest $request)
    {
        //
        try {
            $request->validated();
            $device = Device::where("mac", $request->device_mac)->first();
            if (!$device) {
                return response()->json([
                    "message" => "Unrecognized device!"
                ], 400);
            }

            $employee = Employee::where([
                ["fingerprint_id", "=", $request->fingerprint_id],
                ["device", "=", $request->device_mac],
            ])->first();

            if (!$employee) {
                return response()->json([
                    "message" => "Use a device where you are registered!"
                ], 400);
            }

            $grace = Configuration::where("name", "grace_period")->first();

            $morningIn = Configuration::where("name", "morning_login")->first();
            $morningInTime = Carbon::createFromFormat('H:i:s', $morningIn->value)->format('H:i:s');
            $morningInWithGrace = Carbon::createFromFormat('H:i:s', $morningIn->value)
                ->addMinutes((int)$grace->value)
                ->format('H:i:s');

            $morningOut = Configuration::where("name", "morning_logout")->first();

            $afternoonIn = Configuration::where("name", "afternoon_login")->first();
            $afternoonInTime = Carbon::createFromFormat('H:i:s', $afternoonIn->value)->format('H:i:s');
            $afternoonInWithGrace = Carbon::createFromFormat('H:i:s', $afternoonIn->value)
                ->addMinutes((int)$grace->value)
                ->format('H:i:s');

            $afternoonOut = Configuration::where("name", "afternoon_logout")->first();

            $currentTime = Carbon::createFromFormat('H:i:s', $request->time)->format('H:i:s');

            $user_attendance = Attendance::where([
                ["employee_id", $employee->id],
                ["date", "=", now()->format('Y-m-d')]
            ])->get();

            $create = false;
            $tag = "";
            if ($request->action == "login") {
                if ($currentTime >= $morningInTime  && $currentTime < $morningOut->value) {
                    if ($user_attendance->where('action', 'am_login')->isNotEmpty()) {
                        $message = "Already logged in for the morning!";
                    } else {
                        if ($currentTime <= $morningInWithGrace) {
                            $tag = "present";
                        } else {
                            $tag = "late";
                        }
                        $action = "am_login";
                        $message = "Morning Logged In!";
                        $create = true;
                    }
                } else if ($currentTime >= $afternoonInTime  && $currentTime < $afternoonOut->value) {
                    if ($user_attendance->where('action', 'pm_login')->isNotEmpty()) {
                        $message = "Already logged in for the afternoon!";
                    } else {
                        if ($currentTime <= $afternoonInWithGrace) {
                            $tag = "present";
                        } else {
                            $tag = "late";
                        }
                        $action = "pm_login";
                        $message = "Afternoon Logged In!";
                        $create = true;
                    }
                } else {
                    $message = "Not in the log in timeframe!";
                }
            } else if ($request->action == "logout") {
                if ($currentTime >= $afternoonInTime) {
                    if ($user_attendance->where('action', 'pm_logout')->isNotEmpty()) {
                        $message = "Already logged out for the afternoon!";
                    } else if ($user_attendance->where('action', 'pm_login')->isEmpty()) {
                        $message = "No afternoon login!";
                    } else {
                        $action = "pm_logout";
                        $message = "Afternoon Logged Out!";
                        $create = true;
                    }
                } else if ($currentTime >= $morningInTime) {
                    if ($user_attendance->where('action', 'am_logout')->isNotEmpty()) {
                        $message = "Already logged out for the morning!";
                    } else if ($user_attendance->where('action', 'am_login')->isEmpty()) {
                        $message = "No morning login!";
                    } else {
                        $action = "am_logout";
                        $message = "Morning Logged Out!";
                        $create = true;
                    }
                } else {
                    $message = "Not in the log in timeframe!";
                }
            } else {
                $message = "Invalid Action!";
            }



            if ($create) {
                Attendance::create([
                    "employee_id" => $employee->id,
                    "device_id" => $device->id,
                    "action" => $action,
                    "tag" => $tag ? $tag : null,
                    "date" => now()->format('Y-m-d'),
                    "time" => $request->time
                ]);

                return response()->json([
                    "message" => $message
                ], 200);
            }

            return response()->json([
                "message" => $message
            ], 403);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage()
            ], 403);
        }
    }

    public function show(Request $request)
    {
        if (Auth::user()->type == 1) {
            $attendance = AttendanceResource::collection(
                Attendance::with(['employee.user', 'device'])->get()
            );
        } else {
            $employee = Employee::where("user_id", Auth::user()->id)->first();
            $attendance = AttendanceResource::collection(
                Attendance::with(['employee.user', 'device'])->where("employee_id", "=", $employee->id)->get()
            );
        }
        return inertia("AttendanceLog", [
            "initAttendance" => $attendance
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

    public function attendance(Request $request)
    {
        $user_data = Employee::with(['user', 'position'])
            ->where('user_id', Auth::id())
            ->first();


        $attendance = Attendance::where('employee_id', $user_data->id)
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        $requestedYear = (int) $request->year;
        $requestedMonth = (int) $request->month;

        $startOfMonth = Carbon::createFromDate($requestedYear, $requestedMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($requestedYear, $requestedMonth, 1)->endOfMonth();

        $isCurrentMonth = now()->year === $requestedYear && now()->month === $requestedMonth;
        $lastDay = $isCurrentMonth ? now()->day : $endOfMonth->day;

        $formattedAttendance = [];
        $enrolledDate = Carbon::parse($user_data->created_at)->startOfDay();

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth) && $date->day <= $lastDay; $date->addDay()) {
            if ($date->isWeekend() || $date->gt(now()) || $date->lt($enrolledDate)) {
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

        return response()->json([
            "attendance" => $formattedAttendance
        ], 200);
    }

    public function attendanceYearMonth(Request $request) {
        $employee = Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            return response()->json([], 200);
        }

        $availableDates = Attendance::where('employee_id', $employee->id)
            ->select('date')
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

        return response()->json($availableDates, 200);
    }
}
