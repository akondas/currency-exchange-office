<?php

declare(strict_types=1);

namespace Akondas\CurrencyExchangeOffice\Domain;

use Decimal\Decimal;

final readonly class MonetaryAmount
{
    private function __construct(
        public Decimal $amount,
        public CurrencyCode $currencyCode,
    ) {
    }

    public static function fromString(string $amount, CurrencyCode $currencyCode): self
    {
        return new self(new Decimal($amount), $currencyCode);
    }
}
