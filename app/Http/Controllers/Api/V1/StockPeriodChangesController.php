<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\StockPeriodChangesService;

class StockPeriodChangesController extends Controller
{
    public function __construct(private readonly StockPeriodChangesService $service) {}

    public function __invoke(Company $company)
    {
        $res = $this->service->build($company->id);

        return response()->json($res['payload'], $res['status_code']);
    }
}
