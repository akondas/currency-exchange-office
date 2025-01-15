<?php

declare(strict_types=1);

namespace Akondas\CurrencyExchangeOffice\Domain;

enum CurrencyCode: string
{
    case EUR = 'EUR';
    case GBP = 'GBP';
    case JPY = 'JPY';
    case KWD = 'KWD';
    case PLN = 'PLN';
    case USD = 'USD';
}
