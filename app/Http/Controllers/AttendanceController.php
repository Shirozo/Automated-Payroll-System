<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceLogResource;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\AttendanceTemp;
use App\Models\Configuration;
use App\Models\Device;
use App\Models\Employee;
use App\Models\User;
use App\Services\PdfGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

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
            $user_attendance = AttendanceLog::where([
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
                        // if ($currentTime <= $morningInWithGrace) {
                        //     $tag = "present";
                        // } else {
                        //     $tag = "late";
                        // }
                        $action = "am_login";
                        $message = "Morning Logged In!";
                        $create = true;
                    }
                } else if ($currentTime >= $afternoonInTime  && $currentTime < $afternoonOut->value) {
                    if ($user_attendance->where('action', 'pm_login')->isNotEmpty()) {
                        $message = "Already logged in for the afternoon!";
                    } else {
                        // if ($currentTime <= $afternoonInWithGrace) {
                        //     $tag = "present";
                        // } else {
                        //     $tag = "late";
                        // }
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
                        $message = "No afternoon logout!";
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
                    $message = "Not in the log out timeframe!";
                }
            } else {
                $message = "Invalid Action!";
            }



            if ($create) {
                DB::beginTransaction();

                $has_attendance = Attendance::where([
                    ["employee_id", $employee->id],
                    ["date", "=", now()->format('Y-m-d')]
                ])->first();

                if ($has_attendance) {
                    $has_attendance->update([
                        $action => $request->time
                    ]);
                } else {
                    $attendance_ = Attendance::create([
                        "employee_id" => $employee->id,
                        "device_id" => $device->id,
                        "date" => now()->format('Y-m-d'),
                        $action => $request->time
                    ]);

                    AttendanceTemp::create([
                        "attendance_id" => $attendance_->id
                    ]);
                }

                AttendanceLog::create([
                    "employee_id" => $employee->id,
                    "device_id" => $device->id,
                    "action" => $action,
                    "date" => now()->format("Y-m-d"),
                    "time" => $request->time
                ]);

                DB::commit();
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
            $attendance = AttendanceLogResource::collection(
                AttendanceLog::with(['employee.user', 'device'])->get()
            );

            $employees = Employee::with('user')->get()->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->user->name ?? 'Unknown',
                ];
            });
        } else {
            $employee = Employee::where("user_id", Auth::user()->id)->first();
            $employees = [];
            $attendance = AttendanceLogResource::collection(
                AttendanceLog::with(['employee.user', 'device'])->where("employee_id", "=", $employee->id)->get()
            );
        }
        return inertia("AttendanceLog", [
            "initAttendance" => $attendance,
            "employee" => $employees
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
            ->get();

        return response()->json([
            "attendance" => $attendance
        ], 200);
    }

    public function attendanceYearMonth(Request $request)
    {
        if (Auth::user()->type == 1) {
            $employee = Employee::where('id', $request->id)->first();
        } else {
            $employee = Employee::where('user_id', Auth::id())->first();
        }
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

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048' // Max 2MB
        ]);

        $file = $request->file('file');

        $count = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            $header = array_map('trim', $header);


            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($header) !== count($data)) {
                    continue;
                }

                $row = (object) array_combine($header, $data);

                $mac = trim($row->mac);
                $date = trim($row->date);
                if (Carbon::parse($date)->isWeekday()) {

                    $device = Device::where("mac", "=", $mac)->first();
                    if ($device) {
                        $action = trim($row->action);

                        $has_attendance_entry = Attendance::where([
                            ["device_id", "=", $device->id],
                            ["action", "=", $action],
                            ["date", "=", $date]
                        ])->first();

                        if (!$has_attendance_entry) {
                            $finger_id = trim($row->id);
                            $time = trim($row->time);

                            $employee = Employee::where([
                                ["device", "=", $mac],
                                ["fingerprint_id", "=", $finger_id]
                            ])->first();

                            Attendance::create([
                                "employee_id" => $employee->id,
                                "device_id" => $device->id,
                                "action" => $action,
                                "date" => $date,
                                "time" => $time
                            ]);

                            $count++;
                        }
                    }
                }
            }

            fclose($handle);

            return response()->json([
                'message' => $count . ' attendance have been uploaded successfully!',
                'header' => $header
            ], 200);
        }

        return response()->json(['message' => 'Failed to read the file.'], 500);
    }

    public function generateDTR(Request $request, PdfGeneratorService $pdfService)
    {

        if ($request->has("year") && $request->has("month")) {
            if (Auth::user()->type == 2) {
                $user = Auth::user();
                $employee = Employee::where('user_id', $user->id)->first();
            } else {
                if (!$request->has("employee_id")) {
                    return response()->json(['message' => 'Requires an employee.'], 500);
                }
                $employee = Employee::where("id", $request->employee_id)->first();
                $user = User::where("id", $employee->user_id)->first();
            }

            $monthStr = $request->month;
            $yearInt = $request->year ?? date('Y');

            $dateForMonth = Carbon::parse("1 $monthStr $yearInt");
            $startOfMonth = $dateForMonth->copy()->startOfMonth();
            $endOfMonth = $dateForMonth->copy()->endOfMonth();

            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereYear('date', $dateForMonth->year)
                ->whereMonth('date', $dateForMonth->month)
                ->get();

            if ($attendances->count() <= 0) {
                return response()->json(['message' => 'No Data to Show.'], 500);
            }

            $attendanceData = [];



            for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                $am_in = '';
                $am_out = '';
                $pm_in = '';
                $pm_out = '';
                $hours_rendered = 0;

                if (!$date->isWeekend()) {
                    $dateString = $date->format('Y-m-d');


                    $dayAttendance = $attendances->where("date", $dateString)->first();
                    if ($dayAttendance) {


                        $am_in = $dayAttendance->am_login ?? '';
                        $am_out = $dayAttendance->am_logout ?? '';
                        $pm_in = $dayAttendance->pm_login ?? '';
                        $pm_out = $dayAttendance->pm_logout ?? '';

                        if ($am_in && $am_out) {
                            $hours_rendered += Carbon::parse($am_in)->diffInMinutes(Carbon::parse($am_out)) / 60;
                        }

                        if ($pm_in && $pm_out) {
                            $hours_rendered += Carbon::parse($pm_in)->diffInMinutes(Carbon::parse($pm_out)) / 60;
                        }
                    }
                }



                $attendanceData[] = [
                    'date' => $date->day,
                    'day' => $date->format('D'),
                    'am_in' => $am_in,
                    'am_out' => $am_out,
                    'pm_in' => $pm_in,
                    'pm_out' => $pm_out,
                    'hours_rendered' => round($hours_rendered, 2), // Round to 2 decimal places
                ];
            }

            $data = [
                'employee_name' => $user->name,
                'month' => $dateForMonth->format('F'),
                'year' => $dateForMonth->year,
                'attendance' => $attendanceData,
            ];

            return $pdfService->generateDtrPdf($data);
        } else {
            return response()->json(['message' => 'Failed to create DTR.'], 500);
        }
    }

    public function getAttendance(Request $request, Employee $employee)
    {
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', Carbon::now('Asia/Manila')->month)
            ->whereYear('date', Carbon::now('Asia/Manila')->year)
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($attendances, 200);
    }

    /**
     * Amend bulk attendance records.
     */
    public function amend(Request $request)
    {
        if (Auth::user()->type != 1) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'attendance' => 'required|array',
            'attendance.*.id' => 'required|exists:attendances,id',
            'attendance.*.date' => 'required|date',
            'attendance.*.am_login' => 'nullable|string',
            'attendance.*.am_logout' => 'nullable|string',
            'attendance.*.pm_login' => 'nullable|string',
            'attendance.*.pm_logout' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->attendance as $record) {
                $attendance = Attendance::find($record['id']);

                if (!$attendance) {
                    continue;
                }

                $formatTime = function ($timeStr) {
                    if (empty(trim($timeStr))) return null;
                    if (strlen($timeStr) === 5) return $timeStr . ':00'; // "07:35" to "07:35:00"
                    return $timeStr;
                };

                $am_login = $formatTime($record['am_login'] ?? null);
                $am_logout = $formatTime($record['am_logout'] ?? null);
                $pm_login = $formatTime($record['pm_login'] ?? null);
                $pm_logout = $formatTime($record['pm_logout'] ?? null);

                // 3. Backend Edge Case Validation (Time logical boundaries)
                $checkTime = function($timeStr, $isAm, $isLogout = false) {
                    if (!$timeStr) return true;
                    $hour = (int) substr($timeStr, 0, 2);
                    
                    if ($isAm) {
                        // Allow 12:XX strictly for AM Logout only
                        if ($hour >= 12 && !($isLogout && $hour === 12)) return false; 
                    } else {
                        // Must be 12 PM (noon) or later
                        if ($hour < 12) return false;
                    }
                    return true;
                };

                // Validate AM/PM rules
                if (!$checkTime($am_login, true) || !$checkTime($am_logout, true, true)) {
                    throw new \Exception("Invalid AM time detected for date {$record['date']}.");
                }
                if (!$checkTime($pm_login, false) || !$checkTime($pm_logout, false)) {
                    throw new \Exception("Invalid PM time detected for date {$record['date']}.");
                }

                // Validate Chronology (Ins can't be after Outs)
                if ($am_login && $am_logout && strtotime($am_login) >= strtotime($am_logout)) {
                    throw new \Exception("AM login cannot be later than AM logout on {$record['date']}.");
                }
                if ($pm_login && $pm_logout && strtotime($pm_login) >= strtotime($pm_logout)) {
                    throw new \Exception("PM login cannot be later than PM logout on {$record['date']}.");
                }
                if ($am_logout && $pm_login) {
                    // PM Login is allowed at 12:xx as long as they already logged out for AM, and PM isn't earlier than AM logout
                    if (strtotime($am_logout) > strtotime($pm_login)) {
                        throw new \Exception("AM logout cannot be later than PM login on {$record['date']}.");
                    }
                }

                 $attendance->update([
                    'am_login' => $am_login,
                    'am_logout' => $am_logout,
                    'pm_login' => $pm_login,
                    'pm_logout' => $pm_logout,
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Attendance successfully updated.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 422); // Unprocessable Entity
        }
    }
}
