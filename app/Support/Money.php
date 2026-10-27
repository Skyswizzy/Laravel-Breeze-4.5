<?php

namespace App\Support;

/**
 * Money is calculated in integer cents to avoid floating point rounding errors,
 * then stored as DECIMAL(10,2) strings.
 */
class Money
{
    public static function toCents(string|int|float $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    public static function fromCents(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}
