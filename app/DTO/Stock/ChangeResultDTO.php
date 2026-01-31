<?php

namespace App\DTO\Stock;

final readonly class ChangeResultDTO
{
    public function __construct(
        public ?string $fromUsed,
        public ?string $toUsed,
        public ?float $startPrice,
        public ?float $endPrice,
        public ?float $valueChange,
        public ?float $percentageChange,
    ) {}
}
