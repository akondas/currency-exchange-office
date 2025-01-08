<?php

declare(strict_types=1);

namespace Tests\Akondas\CurrencyExchangeOffice\Unit\Domain;

use Akondas\CurrencyExchangeOffice\Domain\DailyLedger;
use Akondas\CurrencyExchangeOffice\Domain\Event\DailyLedgerOpened;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DailyLedger::class)]
final class DailyLedgerTest extends TestCase
{
    #[Test]
    public function it_will_open_new_daily_ledger()
    {
        $ledger = DailyLedger::open([]);

        self::assertInstanceOf(DailyLedgerOpened::class, $ledger->popRecordedEvents()[0]);
    }
}
