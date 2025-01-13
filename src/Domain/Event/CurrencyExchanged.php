<?php

declare(strict_types=1);

namespace Akondas\CurrencyExchangeOffice\Domain\Event;

use Akondas\CurrencyExchangeOffice\Domain\Event;
use Akondas\CurrencyExchangeOffice\Domain\MonetaryAmount;

final readonly class CurrencyExchanged implements Event
{
    public function __construct(
        public MonetaryAmount $from,
        public MonetaryAmount $to,
    ) {
    }
}
