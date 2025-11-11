<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;
    protected $fillable = [
        "employee_number",
        "deduction_gsis_mpl",
        "deduction_pagibig_mp3",
        "deduction_pagibig_calamity",
        "deduction_city_savings",
        "deduction_withholding_tax",
        "deduction_igp_cottage",
        "deduction_cfi",
        "device",
        "fingerprint_id",
        "position_id",
    ];
}
