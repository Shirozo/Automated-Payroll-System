<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConfigurationRequest;
use App\Http\Requests\UpdateConfigurationDeductionRequest;
use App\Http\Requests\UpdateConfigurationRequest;
use App\Http\Requests\UpdateConfigurationRequestAttendance;
use App\Models\Configuration;
use Illuminate\Http\Request;

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
    public function updateDeduction(UpdateConfigurationDeductionRequest $request)
    {
        //
        $request->validated();
        $local_pave = Configuration::where('name', 'local_pave')->first();
        $pag_ibig_premium = Configuration::where('name', 'pag_ibig_premium')->first();
        $essu_ffa = Configuration::where('name', 'essu_ffa')->first();
        $essu_union = Configuration::where('name', 'essu_union')->first();

        $local_pave->update([
            "value" => $request->local_pave
        ]);

        $pag_ibig_premium->update([
            "value" => $request->pag_ibig_premium
        ]);

        $essu_ffa->update([
            "value" => $request->essu_ffa
        ]);

        $essu_union->update([
            "value" => $request->essu_union
        ]);

        return redirect()->route("configuration.show")->with('success', 'Configuration updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function updateCompensation(Request $request)
    {
        $request->validate([
            "pera" => "required|integer"
        ]);

        $pera = Configuration::where('name', 'pera')->first();

        $pera->update([
            "value" => $request->pera
        ]);

        return redirect()->route("configuration.show")->with('success', 'Configuration updated successfully');
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
