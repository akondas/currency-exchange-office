<?php

declare(strict_types=1);

namespace Akondas\CurrencyExchangeOffice\Domain;

use Decimal\Decimal;

final readonly class MonetaryAmount
{
    private function __construct(
        public Decimal $amount,
        public Currency $currency,
    ) {
    }

    public static function fromString(string $amount, Currency $currency): self
    {
        return new self(new Decimal($amount), $currency);
    }

    public function add(self $other): self
    {
        $this->assertCurrency($other, 'Cannot add amounts in different currencies');

        return new self($this->amount->add($other->amount), $this->currency);
    }

    public function sub(self $other): self
    {
        $this->assertCurrency($other, 'Cannot subtract amounts in different currencies');

        return new self($this->amount->sub($other->amount), $this->currency);
    }

    public function lessThan(self $other): bool
    {
        $this->assertCurrency($other, 'Cannot compare amounts in different currencies');

        return $this->amount->compareTo($other->amount) < 0;
    }

    public function toDecimalString(): string
    {
        return $this->amount->toFixed($this->currency->decimalPlaces);
    }

    public function toCurrencyString(): string
    {
        return sprintf('%s %s', $this->toDecimalString(), $this->currency->code->value);
    }

    private function assertCurrency(self $other, string $message): void
    {
        if (!$this->currency->equals($other->currency)) {
            throw new \InvalidArgumentException($message);
        }
    }
}
