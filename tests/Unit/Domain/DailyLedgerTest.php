<?php

declare(strict_types=1);

namespace Tests\Akondas\CurrencyExchangeOffice\Unit\Domain;

use Akondas\CurrencyExchangeOffice\Domain\CurrencyCode;
use Akondas\CurrencyExchangeOffice\Domain\DailyLedger;
use Akondas\CurrencyExchangeOffice\Domain\Event\CurrencyExchanged;
use Akondas\CurrencyExchangeOffice\Domain\Event\DailyLedgerClosed;
use Akondas\CurrencyExchangeOffice\Domain\Event\DailyLedgerOpened;
use Akondas\CurrencyExchangeOffice\Domain\MonetaryAmount;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DailyLedger::class)]
final class DailyLedgerTest extends TestCase
{
    #[Test]
    public function it_will_open_new_daily_ledger(): void
    {
        $ledger = DailyLedger::open([]);

        self::assertInstanceOf(DailyLedgerOpened::class, $ledger->popRecordedEvents()[0]);
    }

    #[Test]
    public function it_will_exchange_money(): void
    {
        $ledger = DailyLedger::open([MonetaryAmount::fromString('100.00', CurrencyCode::USD)]);
        $ledger->popRecordedEvents();

        $ledger->exchange(MonetaryAmount::fromString('10.00', CurrencyCode::PLN), MonetaryAmount::fromString('40.00', CurrencyCode::USD));

        self::assertInstanceOf(CurrencyExchanged::class, $ledger->popRecordedEvents()[0]);
    }

    #[Test]
    public function it_will_not_allow_to_exchange_money_if_there_is_no_sufficient_funds(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $ledger = DailyLedger::open([MonetaryAmount::fromString('100.00', CurrencyCode::USD)]);
        $ledger->exchange(MonetaryAmount::fromString('40.00', CurrencyCode::PLN), MonetaryAmount::fromString('160.00', CurrencyCode::USD));
    }

    #[Test]
    public function it_will_not_allow_to_exchange_money_if_there_is_no_sufficient_funds_from_the_beginning(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Insufficient funds to deliver 4.00 USD');

        $ledger = DailyLedger::open([]);
        $ledger->exchange(MonetaryAmount::fromString('1.00', CurrencyCode::PLN), MonetaryAmount::fromString('4.00', CurrencyCode::USD));
    }

    #[Test]
    public function it_will_not_allow_to_exchange_the_same_currency(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot exchange same currency');

        $ledger = DailyLedger::open([]);
        $ledger->exchange(MonetaryAmount::fromString('1.00', CurrencyCode::USD), MonetaryAmount::fromString('4.00', CurrencyCode::USD));
    }

    #[Test]
    public function it_will_exchange_money_which_were_obtained_during_the_day(): void
    {
        $ledger = DailyLedger::open([MonetaryAmount::fromString('100.00', CurrencyCode::USD)]);
        $ledger->exchange(MonetaryAmount::fromString('20.00', CurrencyCode::PLN), MonetaryAmount::fromString('80.00', CurrencyCode::USD));
        $ledger->popRecordedEvents();

        $ledger->exchange(MonetaryAmount::fromString('2.00', CurrencyCode::GBP), MonetaryAmount::fromString('10.00', CurrencyCode::PLN));

        self::assertInstanceOf(CurrencyExchanged::class, $ledger->popRecordedEvents()[0]);
    }

    #[Test]
    public function it_will_close_daily_ledger_with_balances(): void
    {
        $ledger = DailyLedger::open([
            MonetaryAmount::fromString('100.00', CurrencyCode::USD),
            MonetaryAmount::fromString('50.00', CurrencyCode::EUR),
        ]);
        $ledger->popRecordedEvents();

        $ledger->close();

        $event = $ledger->popRecordedEvents()[0];
        self::assertInstanceOf(DailyLedgerClosed::class, $event);
        self::assertCount(2, $event->ledgerEntries);
    }

    #[Test]
    public function it_will_check_if_ledger_is_already_closed(): void
    {
        $ledger = DailyLedger::open([]);
        $ledger->close();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Daily ledger is already closed');

        $ledger->close();
    }

    #[Test]
    public function it_will_not_allow_to_exchange_if_ledger_is_closed(): void
    {
        $ledger = DailyLedger::open([]);
        $ledger->close();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Daily ledger is already closed');

        $ledger->exchange(MonetaryAmount::fromString('20.00', CurrencyCode::PLN), MonetaryAmount::fromString('80.00', CurrencyCode::USD));
    }
}
