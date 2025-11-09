<?php

use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(["prefix" => "employee", "as" => "employee.", "middleware" => ["auth"]], function() {
    
    Route::get("/", [EmployeeController::class, "show"])->name("show");
});

Route::group(["prefix" => "position", "as" => "position.", "middleware" => ["auth"]], function() {
    
    Route::get("/", [PositionController::class, "show"])->name("show");

    Route::post('/store', [PositionController::class, "store"])->name("store");

    Route::put("/update/id/{position}", [PositionController::class, "update"])->name("update");

    Route::delete("/delete/id/{position}", [PositionController::class, "destroy"])->name("destroy");
});

Route::group(["prefix" => "configuration", "as" => "configuration.", "middleware" => ["auth"]], function() {

    Route::get('/', [ConfigurationController::class, "show"])->name("show");
});

Route::group(["prefix" => "device", "as" => "device.", "middleware" => ['auth']], function() {

    Route::post('/register', [DeviceController::class, "register"])->name("register");

    Route::get("/online", [DeviceController::class, "online"])->name("online");
});

require __DIR__.'/auth.php';
