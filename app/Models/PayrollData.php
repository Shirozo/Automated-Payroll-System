<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollData extends Model
{
    //
    protected $fillable = [
        "payroll_id",
        "employee_id",
        "rate",
        "pera",
        "period_earned",
        "gsis_mpl",
        "philhealth",
        "local_pave",
        "life_retirement",
        "pagibig_premium",
        "pagibig_mp3",
        "pagibig_calamity",
        "city_savings",
        "absence_wo_pay",
        "withholding_tax",
        "cottage_rental",
        "essu_fa",
        "retiree_asst",
        "essu_union",
        "cfi",
    ];

    public function payroll() {
        return $this->belongsTo(Payroll::class);
    }

    public function employee() {
        return $this->belongsTo(Employee::class);
    }
}
