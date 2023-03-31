<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\JobsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::resource('jobs', JobsController::class)->except(['create', 'edit']);
    Route::get('/employees/{id}/managers', [EmployeeController::class, 'getManagers']);
    Route::get('/employees/{id}/managers-salary', [EmployeeController::class, 'getManagerSalary']);
    Route::get('/employees/search', [EmployeeController::class, 'SearchEmployees']);
    Route::get('/employees/export', [EmployeeController::class, 'ExportEmployees']);
    Route::post('/employees/import', [EmployeeController::class, 'ImportEmployees']);
    Route::resource('employees', EmployeeController::class)->except(['create', 'edit']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'register']);

