<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConfigurationRequest;
use App\Http\Requests\UpdateConfigurationRequest;
use App\Http\Requests\UpdateConfigurationRequestAttendance;
use App\Models\Configuration;

class ConfigurationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConfigurationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Configuration $configuration)
    {
        //
        $config = Configuration::all()->pluck('value', 'name');;

        return inertia("Configuration", [
            "config" => $config
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Configuration $configuration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConfigurationRequest $request, Configuration $configuration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Configuration $configuration)
    {
        //
    }

    public function updateAttendance(UpdateConfigurationRequestAttendance $updateConfigurationRequestAttendance)
    {
        $updateConfigurationRequestAttendance->validated();

        $morning_login = Configuration::where('name', 'morning_login')->first();
        $morning_logout = Configuration::where('name', 'morning_logout')->first();
        $afternoon_login = Configuration::where('name', 'afternoon_login')->first();
        $afternoon_logout = Configuration::where('name', 'afternoon_logout')->first();
        $grace_period = Configuration::where('name', 'grace_period')->first();

        $morning_login->update([
            'value' => $updateConfigurationRequestAttendance->morning_login
        ]);

        $morning_logout->update([
            'value' => $updateConfigurationRequestAttendance->morning_logout
        ]);

        $afternoon_login->update([
            'value' => $updateConfigurationRequestAttendance->afternoon_login
        ]);

        $afternoon_logout->update([
            'value' => $updateConfigurationRequestAttendance->afternoon_logout
        ]);

        $grace_period->update([
            'value' => $updateConfigurationRequestAttendance->grace_time
        ]);

        return redirect()->route("configuration.show")->with('success', 'Configuration updated successfully');
    }
}
