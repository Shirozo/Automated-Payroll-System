<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "device_mac" => "required|mac_address",
            "fingerprint_id" => "required|integer",
            "action" => "required|in:login,logout",
            "am_login" => "nullable|date_format:H:i:s",
            "am_logout" => "nullable|date_format:H:i:s",
            "pm_login" => "nullable|date_format:H:i:s",
            "pm_logout" => "nullable|date_format:H:i:s",
        ];
    }
}
