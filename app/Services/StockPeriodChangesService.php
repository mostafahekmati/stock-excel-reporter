<?php

namespace App\Services;

use App\DTO\Stock\ChangeResultDTO;
use App\DTO\Stock\DateRangeDTO;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class StockPeriodChangesService
{
    public function __construct(
        private readonly StockChangeCalculator $calculator,
        private readonly StockPriceQueryService $query,
    ) {}

    public function build(int $companyId): array
    {
        $latestDate = $this->query->latestDate($companyId);

        if (!$latestDate) {
            return [
                'status_code' => 404,
                'payload' => ['message' => 'No stock prices found for this company.'],
            ];
        }

        $rev = (int) Cache::get($this->revKey($companyId), 1);

        $cacheKey = sprintf('stock_period_changes:v%d:%d:%s', $rev, $companyId, $latestDate);

        $payload = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($companyId, $latestDate) {
            $to = Carbon::parse($latestDate);

            $out = $this->buildRelativePeriods($companyId, $to);
            $out['YTD'] = $this->buildYtd($companyId, $to);
            $out['MAX'] = $this->buildMax($companyId, $to);

            return [
                'company_id' => $companyId,
                'as_of' => $to->toDateString(),
                'periods' => $out,
            ];
        });

        return [
            'status_code' => 200,
            'payload' => $payload,
        ];
    }

    private function buildRelativePeriods(int $companyId, Carbon $to): array
    {
        $periods = [
            '1D'  => $to->copy()->subDay(),
            '1M'  => $to->copy()->subMonth(),
            '3M'  => $to->copy()->subMonths(3),
            '6M'  => $to->copy()->subMonths(6),
            '1Y'  => $to->copy()->subYear(),
            '3Y'  => $to->copy()->subYears(3),
            '5Y'  => $to->copy()->subYears(5),
            '10Y' => $to->copy()->subYears(10),
        ];

        $out = [];
        foreach ($periods as $key => $from) {
            $dto = $this->calculator->change($companyId, new DateRangeDTO($from, $to));
            $out[$key] = $dto ? $this->mapChangeResult($dto) : null;
        }

        return $out;
    }

    private function buildYtd(int $companyId, Carbon $to): ?array
    {
        $start = $this->query->firstPriceInJanuaryOfYear($companyId, (int) $to->year);
        if (!$start) {
            return null;
        }

        $dto = $this->calculator->change(
            $companyId,
            new DateRangeDTO(Carbon::parse($start->date), $to)
        );

        return $dto ? $this->mapChangeResult($dto) : null;
    }

    private function buildMax(int $companyId, Carbon $to): ?array
    {
        $oldest = $this->query->oldestPrice($companyId);
        if (!$oldest) {
            return null;
        }

        $dto = $this->calculator->change(
            $companyId,
            new DateRangeDTO(Carbon::parse($oldest->date), $to)
        );

        return $dto ? $this->mapChangeResult($dto) : null;
    }

    private function mapChangeResult(ChangeResultDTO $dto): array
    {
        return [
            'from_used' => $dto->fromUsed,
            'to_used' => $dto->toUsed,
            'start_price' => $dto->startPrice,
            'end_price' => $dto->endPrice,
            'value_change' => $dto->valueChange,
            'percentage_change' => $dto->percentageChange,
        ];
    }

    private function revKey(int $companyId): string
    {
        return "stock:rev:{$companyId}";
    }
}
