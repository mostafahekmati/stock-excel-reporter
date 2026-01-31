<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\Stock\DateRangeDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StockChangeByDateRangeRequest;
use App\Models\Company;
use App\Services\StockChangeCalculator;
use Illuminate\Support\Facades\Cache;

class StockChangeByDateRangeController extends Controller
{
    public function __construct(private readonly StockChangeCalculator $calculator) {}

    public function __invoke(StockChangeByDateRangeRequest $request, Company $company)
    {
        $range = DateRangeDTO::fromStrings(
            $request->validated('from'),
            $request->validated('to'),
        );

        $rev = (int) Cache::get($this->revKey($company->id), 1);

        $cacheKey = sprintf(
            'stock_change:v%d:%d:%s:%s',
            $rev,
            $company->id,
            $range->from->toDateString(),
            $range->to->toDateString(),
        );

        $result = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($company, $range) {
            return $this->calculator->change($company->id, $range);
        });

        if (!$result) {
            return response()->json(['message' => 'Not enough data for the requested dates.'], 422);
        }

        return response()->json([
            'company_id' => $company->id,
            'from_requested' => $range->from->toDateString(),
            'to_requested' => $range->to->toDateString(),
            'from_used' => $result->fromUsed,
            'to_used' => $result->toUsed,
            'start_price' => $result->startPrice,
            'end_price' => $result->endPrice,
            'value_change' => $result->valueChange,
            'percentage_change' => $result->percentageChange,
        ]);
    }

    private function revKey(int $companyId): string
    {
        return "stock:rev:{$companyId}";
    }
}
