<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ImportStockPrices\ImportFormController;
use App\Http\Controllers\Web\ImportStockPrices\SubmitImportController;
use App\Http\Controllers\Web\ImportStockPrices\ShowImportStatusController;

Route::get('/', fn () => redirect('/imports'));

Route::get('/imports', ImportFormController::class)->name('imports.form');
Route::post('/imports', SubmitImportController::class)->name('imports.submit');
Route::get('/imports/{stockImport}', ShowImportStatusController::class)->name('imports.status');
