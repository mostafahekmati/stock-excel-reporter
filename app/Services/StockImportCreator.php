<?php

namespace App\Services;

use App\Enums\StockImportStatus;
use App\Jobs\ImportStockPricesJob;
use App\Models\StockImport;
use Illuminate\Http\UploadedFile;

class StockImportCreator
{
    public function create(int $companyId, UploadedFile $file): StockImport
    {
        $path = $file->store('imports');

        $import = StockImport::create([
            'company_id' => $companyId,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'status' => StockImportStatus::PENDING,
        ]);

        ImportStockPricesJob::dispatch($import->id);

        return $import;
    }
}
