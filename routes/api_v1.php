<?php


use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\StockChangeByDateRangeController;
use App\Http\Controllers\Api\V1\StockImportShowController;
use App\Http\Controllers\Api\V1\StockImportStoreController;
use App\Http\Controllers\Api\V1\StockPeriodChangesController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::apiResource('companies', CompanyController::class)->only(['index','store','show']);

Route::post('stock-imports', StockImportStoreController::class);
Route::get('stock-imports/{stockImport}', StockImportShowController::class);

Route::get('companies/{company}/stock/change', StockChangeByDateRangeController::class);
Route::get('companies/{company}/stock/period-changes', StockPeriodChangesController::class);
