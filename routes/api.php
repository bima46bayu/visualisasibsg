<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SalesApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\Api\AuthController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

// Apply auth middleware to all sales routes
Route::get('/sales/templates/{type}', [SalesApiController::class, 'downloadTemplate']);
Route::middleware('auth:sanctum')->prefix('sales')->group(function () {
    Route::get('/dashboard', [SalesApiController::class, 'dashboard']);
    Route::get('/master-data', [SalesApiController::class, 'masterData']);
    
    Route::get('/targets', [SalesApiController::class, 'getTargets']);
    Route::post('/targets', [SalesApiController::class, 'storeTarget']);
    Route::put('/targets/{id}', [SalesApiController::class, 'updateTarget']);
    Route::delete('/targets/{id}', [SalesApiController::class, 'destroyTarget']);
    
    Route::get('/realizations', [SalesApiController::class, 'getRealizations']);
    Route::post('/realizations', [SalesApiController::class, 'storeRealization']);
    Route::put('/realizations/{id}', [SalesApiController::class, 'updateRealization']);
    Route::delete('/realizations/{id}', [SalesApiController::class, 'destroyRealization']);
    Route::post('/targets/import', [SalesApiController::class, 'importTargets']);
    Route::get('/targets/export', [SalesApiController::class, 'exportTargets']);
    Route::post('/realizations/import', [SalesApiController::class, 'importRealizations']);
    Route::get('/realizations/export', [SalesApiController::class, 'exportRealizations']);

    // Master CRUD Endpoints
    Route::get('/master/{type}', [SalesApiController::class, 'getMasterList']);
    Route::post('/master/{type}', [SalesApiController::class, 'storeMaster']);
    Route::put('/master/{type}/{id}', [SalesApiController::class, 'updateMaster']);
    Route::delete('/master/{type}/{id}', [SalesApiController::class, 'destroyMaster']);
});

use App\Http\Controllers\Api\UserController;
Route::middleware('auth:sanctum')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

use App\Http\Controllers\Api\ProfitabilityApiController;
Route::middleware('auth:sanctum')->prefix('profitabilities')->group(function () {
    Route::get('/dashboard', [ProfitabilityApiController::class, 'dashboard']);
    Route::get('/export', [ProfitabilityApiController::class, 'export']);
    Route::get('/export-template', [ProfitabilityApiController::class, 'exportTemplate']);
    Route::post('/import', [ProfitabilityApiController::class, 'import']);
    Route::get('/', [ProfitabilityApiController::class, 'index']);
    Route::post('/', [ProfitabilityApiController::class, 'store']);
    Route::put('/{id}', [ProfitabilityApiController::class, 'update']);
    Route::delete('/{id}', [ProfitabilityApiController::class, 'destroy']);
});
