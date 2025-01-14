<?php

declare(strict_types=1);

namespace Tests\Akondas\CurrencyExchangeOffice\Unit\Domain;

use Akondas\CurrencyExchangeOffice\Domain\CurrencyCode;
use Akondas\CurrencyExchangeOffice\Domain\MonetaryAmount;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MonetaryAmount::class)]
final class MonetaryAmountTest extends TestCase
{
    #[Test]
    public function it_will_add_amount(): void
    {
        $ten = MonetaryAmount::fromString('10.00', CurrencyCode::USD);
        $twenty = MonetaryAmount::fromString('20.00', CurrencyCode::USD);

        self::assertSame('30.00', $ten->add($twenty)->toDecimalString());
    }

    #[Test]
    public function it_will_add_amount_only_in_the_same_currency(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        MonetaryAmount::fromString('10.00', CurrencyCode::USD)
            ->add(MonetaryAmount::fromString('20.00', CurrencyCode::EUR));
    }

    #[Test]
    public function it_will_sub_amount(): void
    {
        $thirty = MonetaryAmount::fromString('30.00', CurrencyCode::USD);
        $ten = MonetaryAmount::fromString('10.00', CurrencyCode::USD);

        self::assertSame('20.00', $thirty->sub($ten)->toDecimalString());
    }

    #[Test]
    public function it_will_sub_amount_only_in_the_same_currency(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        MonetaryAmount::fromString('10.00', CurrencyCode::USD)
            ->sub(MonetaryAmount::fromString('20.00', CurrencyCode::EUR));
    }

    #[Test]
    #[DataProvider('lessThanData')]
    public function it_will_allow_to_check_if_less_than(string $first, string $second, bool $result): void
    {
        self::assertSame($result, MonetaryAmount::fromString($first, CurrencyCode::USD)
            ->lessThan(MonetaryAmount::fromString($second, CurrencyCode::USD)));
    }

    /**
     * @return array<int, array{string, string, bool}>
     */
    public static function lessThanData(): array
    {
        return [
            ['10.00', '20.00', true],
            ['19.99', '20.00', true],
            ['20.00', '10.00', false],
            ['10.00', '10.00', false],
            ['10.00', '9.99', false],
            ['10.001', '10.00', false],
            ['0.00', '0.00', false],
            ['-20.00', '-10.00', true],
            ['-10.00', '-20.00', false],
        ];
    }
}
