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

    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function device() {
        return $this->belongsTo(Device::class);
    }
}
