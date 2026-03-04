<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceTemp extends Model
{
    //
    protected $fillable = [
        "attendance_id"
    ];

    public function attendance() {
        return $this->belongsTo(Attendance::class);
    }
}
