<?php

namespace App\DTO\Stock;



use Illuminate\Support\Carbon;

final readonly class DateRangeDTO
{
    public function __construct(
        public Carbon $from,
        public Carbon $to,
    ) {}

    public static function fromStrings(string $from, string $to): self
    {
        return new self(
            from: Carbon::parse($from),
            to: Carbon::parse($to),
        );
    }
}
