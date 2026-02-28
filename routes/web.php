<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get("/test", function() {return response('', 200);});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(["prefix" => "", "as" => "index.", "middleware" => ["auth"]], function() {
    
    // Route::get("/", [EmployeeController::class, "show"])->name("show");

    Route::get("/dashboard", [DashboardController::class, "dashboard"])->name("dashboard");

    Route::get("/dashboard/employee", [DashboardController::class, "employee"])->name("employee");
});

Route::group(["prefix" => "employee", "as" => "employee.", "middleware" => ["auth"]], function() {
    
    Route::get("/", [EmployeeController::class, "show"])->name("show");

    Route::post("/store", [EmployeeController::class, "store"])->name("store");

    Route::put("/update/employee/{employee}", [EmployeeController::class, "update"])->name("update");
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

    Route::get('/register', [DeviceController::class, "register"])->name("register")->withoutMiddleware("auth");

    Route::get("/online", [DeviceController::class, "online"])->name("online");
});

Route::group(["prefix" => "attendance", "as" => "attendance."], function() {

    Route::get("/", [AttendanceController::class, "show"])->name("show");
    
    Route::post("/store", [AttendanceController::class, "store"])->name("store")
    ->withoutMiddleware([VerifyCsrfToken::class, "auth"]);

    Route::get("/all", [AttendanceController::class, "attendance"])
    ->middleware("auth")->name("all");

    Route::get("/year-month", [AttendanceController::class, "attendanceYearMonth"])
    ->middleware("auth")->name("year-month");

     Route::post("/upload", [AttendanceController::class, "upload"])
    ->middleware("auth")->name("upload");

});

require __DIR__.'/auth.php';
