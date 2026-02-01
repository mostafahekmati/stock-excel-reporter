<?php

namespace App\Http\Controllers\Web\ImportStockPrices;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreStockImportRequest;
use App\Services\StockImportCreator;

class SubmitImportController extends Controller
{
    public function __construct(private readonly StockImportCreator $creator) {}

    public function __invoke(StoreStockImportRequest $request)
    {
        $import = $this->creator->create(
            companyId: (int) $request->validated('company_id'),
            file: $request->file('file'),
        );

        return redirect()->route('imports.status', ['stockImport' => $import->id])
            ->with('status', 'Upload successful. Import started in background.');
    }
}
