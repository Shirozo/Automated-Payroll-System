<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;

    protected $fillable = [
        "employee_id",
        "device_id",
        "action",
        "date",
        "tag",
        "time"
    ];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function device() {
        return $this->belongsTo(Device::class);
    }
}
