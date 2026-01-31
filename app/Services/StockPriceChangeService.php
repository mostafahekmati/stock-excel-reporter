<?php

namespace App\Services;

use App\Models\StockPrice;
use Illuminate\Support\Carbon;

class StockPriceChangeService
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

    public function change(int $companyId, Carbon $from, Carbon $to): array
    {
        $start = $this->priceAtOrAfter($companyId, $from);
        $end   = $this->priceAtOrBefore($companyId, $to);

        if (!$start || !$end) {
            return [
                'start' => $start,
                'end' => $end,
                'value_change' => null,
                'percentage_change' => null,
            ];
        }

        $startPrice = (float) $start->price;
        $endPrice   = (float) $end->price;

        $valueChange = $endPrice - $startPrice;
        $percentageChange = $startPrice == 0.0 ? null : ($valueChange / $startPrice) * 100;

        return [
            'start' => $start,
            'end' => $end,
            'value_change' => $valueChange,
            'percentage_change' => $percentageChange,
        ];
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

    // ✅ برای MAX: قدیمی‌ترین قیمت موجود
    public function oldestPrice(int $companyId): ?StockPrice
    {
        return StockPrice::query()
            ->where('company_id', $companyId)
            ->orderBy('date')
            ->first();
    }

}
