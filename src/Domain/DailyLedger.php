<?php

declare(strict_types=1);

namespace Akondas\CurrencyExchangeOffice\Domain;

use Akondas\CurrencyExchangeOffice\Domain\Event\DailyLedgerOpened;

final class DailyLedger
{
    /**
     * @var array<Event>
     */
    private array $recordedEvents = [];

    public static function open(array $ledgerEntries): self
    {
        $instance = new self();
        $instance->recordThat(new DailyLedgerOpened($ledgerEntries));

        return $instance;
    }

    /**
     * @return Event[]
     */
    public function popRecordedEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    private function recordThat(Event $event): void
    {
        $this->recordedEvents[] = $event;
    }
}
