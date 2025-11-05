<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Models\Position;

class PositionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePositionRequest $request)
    {
        //
        $request->validated();

        $pos = Position::create($request->validated());

        return redirect()->route("position.show")->with([
            "success" => "Position Created!",
            "position" => $pos
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        //
        $positions = Position::all();
        return inertia("Position", [
            "positions" => $positions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePositionRequest $request, Position $position)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        //
    }
}
