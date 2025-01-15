<?php

declare(strict_types=1);

namespace Akondas\CurrencyExchangeOffice\Domain;

use Akondas\CurrencyExchangeOffice\Domain\Event\CurrencyExchanged;
use Akondas\CurrencyExchangeOffice\Domain\Event\DailyLedgerClosed;
use Akondas\CurrencyExchangeOffice\Domain\Event\DailyLedgerOpened;

final class DailyLedger
{
    /**
     * @var array<string, MonetaryAmount>
     */
    private array $ledgerEntries = [];

    private bool $closed = false;

    /**
     * @var array<Event>
     */
    private array $recordedEvents = [];

    private function __construct()
    {
    }

    /**
     * @param array<MonetaryAmount> $ledgerEntries
     */
    public static function open(array $ledgerEntries): self
    {
        $instance = new self();
        $instance->recordThat(new DailyLedgerOpened($ledgerEntries));

        return $instance;
    }

    public function exchange(MonetaryAmount $source, MonetaryAmount $target): void
    {
        $this->assertNotClosed();

        if ($source->currencyCode === $target->currencyCode) {
            throw new \InvalidArgumentException('Cannot exchange same currency');
        }

        if ($this->getAmount($target->currencyCode)->lessThan($target)) {
            throw new \InvalidArgumentException(sprintf('Insufficient funds to deliver %s', $target->toCurrencyString()));
        }

        $this->recordThat(new CurrencyExchanged($source, $target));
    }

    public function close(): void
    {
        $this->assertNotClosed();

        $this->recordThat(new DailyLedgerClosed($this->ledgerEntries));
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
        $this->apply($event);
    }

    private function apply(Event $event): void
    {
        match ($event::class) {
            DailyLedgerOpened::class => $this->applyDailyLedgerOpened($event),
            CurrencyExchanged::class => $this->applyCurrencyExchanged($event),
            DailyLedgerClosed::class => $this->applyDailyLedgerClosed($event),
            default => null,
        };
    }

    private function applyDailyLedgerOpened(DailyLedgerOpened $event): void
    {
        foreach ($event->ledgerEntries as $entry) {
            $this->ledgerEntries[$entry->currencyCode->value] = $entry;
        }
    }

    private function applyCurrencyExchanged(CurrencyExchanged $event): void
    {
        $this->ledgerEntries[$event->source->currencyCode->value] = $this->getAmount($event->source->currencyCode)->add($event->source);
        $this->ledgerEntries[$event->target->currencyCode->value] = $this->getAmount($event->target->currencyCode)->sub($event->target);
    }

    private function applyDailyLedgerClosed(DailyLedgerClosed $event): void
    {
        $this->closed = true;
    }

    private function getAmount(CurrencyCode $currencyCode): MonetaryAmount
    {
        return $this->ledgerEntries[$currencyCode->value] ?? MonetaryAmount::fromString('0', $currencyCode);
    }

    private function assertNotClosed(): void
    {
        if ($this->closed) {
            throw new \LogicException('Daily ledger is already closed');
        }
    }
}
