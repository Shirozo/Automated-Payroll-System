<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConfigurationRequest;
use App\Http\Requests\UpdateConfigurationRequest;
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
}
