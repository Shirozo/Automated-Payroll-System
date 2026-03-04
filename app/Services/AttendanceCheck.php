<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceTemp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceCheck
{
    public function checkAttendance()
    {
        $attendance_temp = AttendanceTemp::all();

        $currentTime = Carbon::now("Asia/Manila");

        if ($currentTime->format("H") >= 19 || $currentTime->format("H") < 5) {

            DB::beginTransaction();

            foreach ($attendance_temp as $attendance_id) {
                $attendance = Attendance::where("id", $attendance_id->attendance_id)->first();
                
                $attendance->update([
                    "tag" => ($attendance->am_login && $attendance->am_logout && $attendance->pm_login && $attendance->pm_logout) 
                        ? "present" 
                        : "absent"
                ]);

                $attendance_id->delete();
            }
            DB::commit();
        }
    }
}
