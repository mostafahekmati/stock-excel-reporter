<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreStockImportRequest;
use App\Http\Resources\Api\V1\StockImportResource;
use App\Services\StockImportCreator;

class StockImportStoreController extends Controller
{
    public function __construct(private readonly StockImportCreator $creator) {}

    public function __invoke(StoreStockImportRequest $request)
    {
        $import = $this->creator->create(
            companyId: (int) $request->validated('company_id'),
            file: $request->file('file'),
        );

        return response()->json([
            'import' => StockImportResource::make($import),
            'message' => 'Import started in background.',
        ], 202);
    }
}
