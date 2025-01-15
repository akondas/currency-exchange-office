<?php

declare(strict_types=1);

namespace Akondas\CurrencyExchangeOffice\Domain;

final readonly class Currency
{
    public function __construct(
        public CurrencyCode $code,
        public int $decimalPlaces,
    ) {
    }

    public static function EUR(): self
    {
        return new self(CurrencyCode::EUR, 2);
    }

    public static function GBP(): self
    {
        return new self(CurrencyCode::GBP, 2);
    }

    public static function PLN(): self
    {
        return new self(CurrencyCode::PLN, 2);
    }

    public static function USD(): self
    {
        return new self(CurrencyCode::USD, 2);
    }

    public static function KWD(): self
    {
        return new self(CurrencyCode::KWD, 3);
    }

    public static function JPY(): self
    {
        return new self(CurrencyCode::JPY, 0);
    }

    public function equals(self $other): bool
    {
        return $this->code === $other->code;
    }
}
