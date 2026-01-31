<?php

namespace App\Services;

use App\DTO\Stock\ChangeResultDTO;
use App\DTO\Stock\DateRangeDTO;

class StockChangeCalculator
{
    public function __construct(private readonly StockPriceQueryService $query) {}

    public function change(int $companyId, DateRangeDTO $range): ?ChangeResultDTO
    {
        $start = $this->query->priceAtOrAfter($companyId, $range->from);
        $end   = $this->query->priceAtOrBefore($companyId, $range->to);

        if (!$start || !$end) {
            return null;
        }

        $old = (float) $start->price;
        $new = (float) $end->price;

        $valueChange = $new - $old;
        $percentageChange = $old == 0.0 ? null : (($new / $old) - 1) * 100;

        return new ChangeResultDTO(
            fromUsed: $start->date->toDateString(),
            toUsed: $end->date->toDateString(),
            startPrice: $old,
            endPrice: $new,
            valueChange: $valueChange,
            percentageChange: $percentageChange,
        );
    }
}
