<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SettingTableController;
use App\Http\Controllers\TableController;
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

Route::prefix('v1')->group(function() {
    // AUTHENTICATION
    Route::prefix('auth')->group(function() {
        Route::post('login', [AuthController::class, 'login']);
    });

    // TABLES
    Route::prefix('tables')->group(function() {
        Route::post('', [TableController::class, 'create']);
        Route::delete('/{id}', [TableController::class, 'delete']);
    });

    // SETTING TABLE
    Route::prefix('setting-table')->group(function() {
        Route::post('', [SettingTableController::class, 'create']);
        Route::patch('/{id}', [SettingTableController::class, 'update']);
        Route::get('', [SettingTableController::class, 'find_many']);
    });

    // EMPLOYEE (WITH STAFF ROLE)
    Route::prefix('employees')->group(function() {
        Route::post('', [EmployeeController::class, 'create']);
        Route::get('', [EmployeeController::class, 'find_many']);
    });  
});
