<?php

namespace App\Services;

use App\Models\StockPrice;
use Illuminate\Support\Carbon;

class StockPriceQueryService
{
    public function priceAtOrAfter(int $companyId, Carbon $date): ?StockPrice
    {
        return StockPrice::query()
            ->where('company_id', $companyId)
            ->whereDate('date', '>=', $date->toDateString())
            ->orderBy('date')
            ->first();
    }

    public function priceAtOrBefore(int $companyId, Carbon $date): ?StockPrice
    {
        return StockPrice::query()
            ->where('company_id', $companyId)
            ->whereDate('date', '<=', $date->toDateString())
            ->orderByDesc('date')
            ->first();
    }

    public function latestDate(int $companyId): ?string
    {
        return StockPrice::query()
            ->where('company_id', $companyId)
            ->max('date');
    }

    public function oldestPrice(int $companyId): ?StockPrice
    {
        return StockPrice::query()
            ->where('company_id', $companyId)
            ->orderBy('date')
            ->first();
    }

    public function firstPriceInJanuaryOfYear(int $companyId, int $year): ?StockPrice
    {
        return StockPrice::query()
            ->where('company_id', $companyId)
            ->whereYear('date', $year)
            ->whereMonth('date', 1)
            ->orderBy('date')
            ->first();
    }
}
