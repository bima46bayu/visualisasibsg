<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\SalesMemberController;
use App\Http\Controllers\SalesTargetController;
use App\Http\Controllers\SalesRealizationController;
use App\Http\Controllers\SalesManagementController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('master')->group(function () {
    Route::resource('teams', TeamController::class);
    Route::resource('entities', EntityController::class);
    Route::resource('sales-members', SalesMemberController::class);
});

Route::get('/sales-management', [SalesManagementController::class, 'index'])->name('sales-management.index');

Route::prefix('sales')->group(function () {
    Route::post('targets/import', [SalesTargetController::class, 'import'])->name('sales-targets.import');
    Route::resource('targets', SalesTargetController::class)->names('sales-targets');
    
    Route::post('realizations/import', [SalesRealizationController::class, 'import'])->name('sales-realizations.import');
    Route::resource('realizations', SalesRealizationController::class)->names('sales-realizations');
});
