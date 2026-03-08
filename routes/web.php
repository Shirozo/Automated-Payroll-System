<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SsoController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get("/test", function () {
    return response('', 200);
});

// Only uncomment when using central authentication server
// Route::middleware('web')->group(function () {
//     Route::get('/login', [SsoController::class, 'redirect'])->name('login');
//     Route::get('/auth/callback', [SsoController::class, 'callback']);
// });

Route::get("/", function () {
    return inertia("Guest");
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(["prefix" => "", "as" => "index.", "middleware" => ["auth"]], function () {

    Route::get("/dashboard", [DashboardController::class, "dashboard"])->name("dashboard");

    Route::get("/dashboard/employee", [DashboardController::class, "employee"])->name("employee");
});

Route::group(["prefix" => "employee", "as" => "employee.", "middleware" => ["auth"]], function () {

    Route::get("/", [EmployeeController::class, "show"])->name("show");

    Route::post("/store", [EmployeeController::class, "store"])->name("store");

    Route::put("/update/employee/{employee}", [EmployeeController::class, "update"])->name("update");

    Route::put("/update/device/id/{employee}", [EmployeeController::class, "updateDevice"])->name("device");
});

Route::group(["prefix" => "position", "as" => "position.", "middleware" => ["auth"]], function () {

    Route::get("/", [PositionController::class, "show"])->name("show");

    Route::post('/store', [PositionController::class, "store"])->name("store");

    Route::put("/update/id/{position}", [PositionController::class, "update"])->name("update");

    Route::delete("/delete/id/{position}", [PositionController::class, "destroy"])->name("destroy");
});

Route::group(["prefix" => "configuration", "as" => "configuration.", "middleware" => ["auth"]], function () {

    Route::get('/', [ConfigurationController::class, "show"])->name("show");

    Route::put("/update/attendance", [ConfigurationController::class, "updateAttendance"])->name("updateAttendance");
});

Route::group(["prefix" => "device", "as" => "device.", "middleware" => ['auth']], function () {

    Route::get('/register', [DeviceController::class, "register"])->name("register")->withoutMiddleware("auth");

    Route::get("/online", [DeviceController::class, "online"])->name("online");
});

Route::group(["prefix" => "attendance", "as" => "attendance."], function () {

    Route::get("/", [AttendanceController::class, "show"])->name("show")->middleware("auth");

    Route::post("/store", [AttendanceController::class, "store"])->name("store")
        ->withoutMiddleware([VerifyCsrfToken::class, "auth"]);

    Route::get("/all", [AttendanceController::class, "attendance"])
        ->middleware("auth")->name("all");

    Route::get("/year-month", [AttendanceController::class, "attendanceYearMonth"])
        ->middleware("auth")->name("year-month");

    Route::post("/upload", [AttendanceController::class, "upload"])
        ->middleware("auth")->name("upload");

    Route::get("/generate/dtr", [AttendanceController::class, "generateDTR"])->name("dtr")
        ->middleware("auth");

    Route::get("/get/employee/id/{employee}", [AttendanceController::class, "getAttendance"])
        ->name("getAttendance")->middleware("auth");

    Route::post('/attendance/amend', [AttendanceController::class, 'amend'])->name('amend')->middleware("auth");
});

Route::group(["prefix" => "payroll", "as" => "payroll.", "middleware" => ["auth"]], function () {

    Route::get("/", [PayrollController::class, "show"])->name("show");

    Route::post("/store", [PayrollController::class, "store"])->name("store");

    Route::delete("/delete/id/{payroll}", [PayrollController::class, "destroy"])->name("destroy");

    Route::get("/view/id/{payroll}", [PayrollController::class, "view"])->name("view");
    
    Route::put("/set/visible/id/{payroll}", [PayrollController::class, "updateVisible"])->name("update-view");

    Route::get("/view/id/{payroll}/employee/{employee}", [PayrollController::class, "getPaySlip"])->name("getPaySlip");
});


// Only uncomment when using central authentication server
// Route::post('/sso/logout', function (Request $request) {
//     // Verify the request came from your Auth-Server
//     if ($request->bearerToken() !== env('SSO_WEBHOOK_SECRET', 'your-super-secret-key')) {
//         return response()->json(['error' => 'Unauthorized'], 401);
//     }

//     $userId = $request->input('user_id');

//     if ($userId) {
//         DB::table('sessions')->where('user_id', $userId)->delete();
//     }

//     return response()->json(['status' => 'logged out']);
// });

require __DIR__ . '/auth.php';
