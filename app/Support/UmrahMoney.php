<?php

namespace App\Support;

use InvalidArgumentException;

final class UmrahMoney
{
    public static function cents(string|int $amount): int
    {
        if (! preg_match('/^([0-9]{1,10})(?:\.([0-9]{1,2}))?$/D', (string) $amount, $matches)) {
            throw new InvalidArgumentException('Invalid monetary amount.');
        }

        return ((int) $matches[1] * 100) + (int) str_pad($matches[2] ?? '', 2, '0');
    }

    public static function decimal(int $cents): string
    {
        return ($cents < 0 ? '-' : '').intdiv(abs($cents), 100).'.'.str_pad((string) (abs($cents) % 100), 2, '0', STR_PAD_LEFT);
    }
}
