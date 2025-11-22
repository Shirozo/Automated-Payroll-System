<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->type == 1;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            "name" => "required|string",
            "employee_number" => "required|string",
            "position_id" => "required|numeric",
            "deduction_gsis_mpl" => "required|min:0|numeric",
            "deduction_pagibig_mp3" => "required|min:0|numeric",
            "deduction_pagibig_calamity" => "required|min:0|numeric",
            "deduction_city_savings" => "required|min:0|numeric",
            "deduction_withholding_tax" => "required|min:0|numeric",
            "deduction_igp_cottage" => "required|min:0|numeric",
            "deduction_cfi" => "required|min:0|numeric",
            "device" => "nullable|mac_address",
            "fingerprint_id" => "nullable|integer",
            "password" => "nullable|min:8" 
        ];
    }
}
