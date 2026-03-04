<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    //
    protected $fillable = [
        "employee_id",
        "device_id",
        "action",
        "date",
        "time"
    ];
}
