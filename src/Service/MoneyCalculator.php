<?php

declare(strict_types=1);

namespace CommissionTask\Service;

class MoneyCalculator
{
    public function roundUpMul(string $multiplier1, string $multiplier2): string
    {
        return $this->roundUpMoney(bcmul($multiplier1, $multiplier2, 4));
    }

    public function roundUpDiv(string $dividend, string $divider): string
    {
        return $this->roundUpMoney(bcdiv($dividend, $divider, 10));
    }

    public function sub($minuend, $subtrahend): string
    {
        return bcsub($minuend, $subtrahend, 2);
    }

    public function add($addend1, $addend2): string
    {
        return bcadd($addend1, $addend2, 2);
    }

    public function roundUpMoney(string $value): string
    {
        $round = pow(10, 2);

        return bcdiv((string) ceil($value * $round), (string) $round, 2);
    }
}
