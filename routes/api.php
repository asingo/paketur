<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\ManagerController;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterController::class)->name('register');
Route::post('/login', LoginController::class)->name('login');
Route::post('/logout', LogoutController::class)->middleware('auth:api')->name('logout');

Route::middleware(['auth:api','role:super_admin'])->group(function () {
    Route::prefix('company')->group(function () {
        Route::controller(CompanyController::class)->group(function () {
            Route::post('/create', 'store')->name('company.create');
            Route::put('/update/{id}', 'update')->name('company.update');
            Route::delete('/delete/{id}', 'destroy')->name('company.delete');
            Route::get('/','show')->name('company.show');
        });
    });
});
Route::middleware(['auth:api','role:manager'])->group(function () {
    Route::prefix('manager')->group(function () {
        Route::controller(ManagerController::class)->group(function () {
            Route::post('/create', 'store')->name('manager.create');
            Route::put('/update/{id}', 'update')->name('manager.update');
            Route::delete('/delete/{id}', 'destroy')->name('manager.delete');
            Route::get('/','show')->name('manager.show');
            Route::get('/show/{id}', 'show')->name('manager.get');
        });
    });
    Route::prefix('employee')->group(function () {
        Route::controller(EmployeeController::class)->group(function () {
            Route::post('/create', 'store')->name('employee.create');
            Route::put('/update/{id}', 'update')->name('employee.update');
            Route::delete('/delete/{id}', 'destroy')->name('employee.delete');
        });
    });
});
Route::middleware(['auth:api','role:employee'])->group(function () {
    Route::prefix('employee')->group(function () {
        Route::controller(EmployeeController::class)->group(function () {
           Route::get('/','show')->name('employee.show');
           Route::get('/show/{id}', 'get')->name('employee.get');
        });
    });
});


