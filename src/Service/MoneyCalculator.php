<?php

declare(strict_types=1);

namespace CommissionTask\Service;

class MoneyCalculator
{
    public static function roundUpMul(string $multiplier1, string $multiplier2): string
    {
        return MoneyCalculator::roundUpMoney(bcmul($multiplier1, $multiplier2, 4));
    }

    public static function roundUpDiv(string $dividend, string $divider): string
    {
        return MoneyCalculator::roundUpMoney(bcdiv($dividend, $divider, 10));
    }

    public static function roundUpMoney(string $value): string
    {
        $round = pow(10, 2);

        return bcdiv((string) ceil($value * $round), "$round", 2);
    }
}
