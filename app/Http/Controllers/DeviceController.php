<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    //

    public function register(Request $request)
    {
        $validated = $request->validate([
            'mac' => 'required|string',
            'ip' => 'required|ip',
            'name' => 'required|string',
        ]);

        $device = Device::updateOrCreate(
            ['mac' => $validated['mac']],
            [
                'name' => $validated['name'],
                'ip' => $validated['ip'],
                'status' => 'online',
                'last_seen' => Carbon::now("Asia/Manila")
            ]
        );

        return response()->json([
            'success' => true,
            'device_id' => $device->id,
            'message' => 'Device registered successfully'
        ]);
    }

    public function online(Request $request)
    {
        $onlineThreshold = Carbon::now("Asia/Manila")->subMinutes(2);

        // $devices = Device::where('status', 'online')
        //     ->where('last_seen', '>=', $onlineThreshold)
        //     ->get();

        $devices = Device::where('last_seen', '>=', $onlineThreshold)->get();

        return response()->json([
            'success' => true,
            'devices' => $devices,
        ]);
    }
}
