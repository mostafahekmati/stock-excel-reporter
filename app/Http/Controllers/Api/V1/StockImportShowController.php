<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\StockImportResource;
use App\Models\StockImport;

class StockImportShowController extends Controller
{
    public function __invoke(StockImport $stockImport): StockImportResource
    {
        return StockImportResource::make($stockImport);
    }
}
