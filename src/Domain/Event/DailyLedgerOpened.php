<?php

declare(strict_types=1);

namespace Akondas\CurrencyExchangeOffice\Domain\Event;

use Akondas\CurrencyExchangeOffice\Domain\Event;
use Akondas\CurrencyExchangeOffice\Domain\MonetaryAmount;

final readonly class DailyLedgerOpened implements Event
{
    /**
     * @param array<MonetaryAmount> $ledgerEntries
     */
    public function __construct(
        public array $ledgerEntries,
    ) {
    }
}
