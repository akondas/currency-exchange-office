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

    public function add(self $other): self
    {
        $this->assertCurrency($other, 'Cannot add amounts in different currencies');

        return new self($this->amount->add($other->amount), $this->currencyCode);
    }

    public function sub(self $other): self
    {
        $this->assertCurrency($other, 'Cannot subtract amounts in different currencies');

        return new self($this->amount->sub($other->amount), $this->currencyCode);
    }

    public function lessThan(self $other): bool
    {
        $this->assertCurrency($other, 'Cannot compare amounts in different currencies');

        return $this->amount->compareTo($other->amount) < 0;
    }

    public function toDecimalString(): string
    {
        return $this->amount->toString();
    }

    private function assertCurrency(self $other, string $message): void
    {
        if ($this->currencyCode !== $other->currencyCode) {
            throw new \InvalidArgumentException($message);
        }
    }
}
