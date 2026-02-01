<?php

namespace App\Http\Controllers\Web\ImportStockPrices;

use App\Http\Controllers\Controller;
use App\Models\StockImport;

class ShowImportStatusController extends Controller
{
    public function __invoke(StockImport $stockImport)
    {
        return view('imports.status', [
            'import' => $stockImport->fresh(),
        ]);
    }
}
