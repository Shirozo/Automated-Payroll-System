<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateConfigurationRequestAttendance extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool)Auth::user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "morning_login" => "required|string",
            "morning_logout" => "required|string",
            "afternoon_login" => "required|string",
            "afternoon_logout" => "required|string",
            "grace_time" => "required|integer",
        ];
    }
}
